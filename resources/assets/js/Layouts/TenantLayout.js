import React, { useState, useRef, useCallback } from 'react';
import SidebarDesktop from '../components/SidebarDesktop';
import SidebarMobile from '../components/SidebarMobile';
import { usePage } from '@inertiajs/react';
import GlobalTrackPlayer from '../components/Audio/GlobalTrackPlayer';
import { PlayerContext } from '../contexts/player-context';
import * as Tone from 'tone';
import ImpersonateUserModal from '../components/ImpersonateUserModal';
import LayoutTopBar from '../components/LayoutTopBar';
import ToastFlash from '../components/ToastFlash';
import { useMediaQuery } from 'react-responsive';
import usePromptBeforeUnload from '../hooks/usePromptBeforeUnload';
import useRoute from '../hooks/useRoute';
import SwitchChoirMenu from '../components/SwitchChoirMenu';
import BillingNotices from '../components/BillingNotices';
import TenantNotice from '../components/TenantNotice';
import { ErrorBoundary } from '@sentry/react';
import OuterPageErrorFallback from './OuterPageErrorFallback';

export default function TenantLayout({ children }) {
	const [sidebarOpen, setSidebarOpen] = useState(false);
	const { route } = useRoute();

	const [playerState, setPlayerState] = useState({
		songTitle: null,
		songId: 0,
		fileName: null,
		src: null,
		playing: false,
		loading: false,
		duration: 0,
		volume: 1,
		rate: 1,
		showFullscreen: false,
	});

	const playerRef = useRef(null); // Tone.Player instance
	const pitchShiftRef = useRef(null); // Tone.PitchShift node (persists across tracks)
	const startedAtRef = useRef(null); // AudioContext time when current playback segment began
	const startOffsetRef = useRef(0); // Buffer offset (seconds) at the start of current segment
	const rateRef = useRef(1); // Current playback rate
	const volumeRef = useRef(1); // Current volume (0–1)
	const endTimeoutRef = useRef(null); // setTimeout handle for end-of-track detection

	// Lazily creates a PitchShift node connected to the destination and reuses it across tracks.
	const getPitchShift = useCallback(() => {
		if (!pitchShiftRef.current) {
			pitchShiftRef.current = new Tone.PitchShift({ pitch: 0, windowSize: 0.3 }).toDestination();
		}
		return pitchShiftRef.current;
	}, []);

	const clearEndTimeout = useCallback(() => {
		if (endTimeoutRef.current) {
			clearTimeout(endTimeoutRef.current);
			endTimeoutRef.current = null;
		}
	}, []);

	// Schedule a timeout to mark playback as ended when the track finishes naturally.
	// Reads current values from refs so it can be called without capturing closure state.
	const scheduleEnd = useCallback(() => {
		clearEndTimeout();
		const duration = playerRef.current?.buffer?.duration ?? 0;
		if (!duration || startedAtRef.current === null) return;
		const remaining = (duration - startOffsetRef.current) / rateRef.current;
		endTimeoutRef.current = setTimeout(() => {
			startOffsetRef.current = 0;
			startedAtRef.current = null;
			setPlayerState(prev => ({ ...prev, playing: false }));
		}, remaining * 1000);
	}, [clearEndTimeout]);

	// Returns current playback position in seconds by computing elapsed time from refs.
	const getPosition = useCallback(() => {
		if (startedAtRef.current !== null) {
			const elapsed = (Tone.now() - startedAtRef.current) * rateRef.current;
			const duration = playerRef.current?.buffer?.duration ?? 0;
			return Math.min(startOffsetRef.current + elapsed, duration);
		}
		return startOffsetRef.current;
	}, []);

	const stop = useCallback(() => {
		clearEndTimeout();
		if (playerRef.current) {
			playerRef.current.stop();
			playerRef.current.dispose();
			playerRef.current = null;
		}
		startedAtRef.current = null;
		startOffsetRef.current = 0;
		setPlayerState(prev => ({
			...prev,
			songTitle: null,
			songId: 0,
			fileName: null,
			src: null,
			playing: false,
			loading: false,
			duration: 0,
		}));
	}, [clearEndTimeout]);

	const play = useCallback(
		async attachment => {
			clearEndTimeout();
			if (playerRef.current) {
				playerRef.current.stop();
				playerRef.current.dispose();
			}
			playerRef.current = null;
			startedAtRef.current = null;
			startOffsetRef.current = 0;

			const src = attachment.download_url;
			setPlayerState(prev => ({
				...prev,
				songTitle: attachment.song.title,
				songId: attachment.song.id,
				fileName: attachment.title !== '' ? attachment.title : attachment.filepath,
				src,
				loading: true,
				playing: false,
			}));

			await Tone.start();

			const tonePlayer = new Tone.Player({
				url: src,
				autostart: false,
				onload: () => {
					if (playerRef.current !== tonePlayer) return;
					const duration = tonePlayer.buffer.duration;
					tonePlayer.playbackRate = rateRef.current;
					tonePlayer.volume.value = Tone.gainToDb(volumeRef.current);
					tonePlayer.start();
					startedAtRef.current = Tone.now();
					startOffsetRef.current = 0;
					setPlayerState(prev => ({ ...prev, loading: false, playing: true, duration }));
					scheduleEnd();
				},
				onerror: () => {
					if (playerRef.current !== tonePlayer) return;
					setPlayerState(prev => ({ ...prev, loading: false }));
				},
			}).connect(getPitchShift());

			playerRef.current = tonePlayer;
		},
		[clearEndTimeout, scheduleEnd, getPitchShift]
	);

	const pause = useCallback(() => {
		if (!playerRef.current || startedAtRef.current === null) return;
		clearEndTimeout();
		const elapsed = (Tone.now() - startedAtRef.current) * rateRef.current;
		const duration = playerRef.current.buffer?.duration ?? Infinity;
		startOffsetRef.current = Math.min(startOffsetRef.current + elapsed, duration);
		startedAtRef.current = null;
		playerRef.current.stop();
		setPlayerState(prev => ({ ...prev, playing: false }));
	}, [clearEndTimeout]);

	const togglePlayPause = useCallback(() => {
		if (!playerRef.current) return;
		if (startedAtRef.current !== null) {
			clearEndTimeout();
			const elapsed = (Tone.now() - startedAtRef.current) * rateRef.current;
			const duration = playerRef.current.buffer?.duration ?? Infinity;
			startOffsetRef.current = Math.min(startOffsetRef.current + elapsed, duration);
			startedAtRef.current = null;
			playerRef.current.stop();
			setPlayerState(prev => ({ ...prev, playing: false }));
		} else {
			const offset = startOffsetRef.current;
			playerRef.current.start(Tone.now(), offset);
			startedAtRef.current = Tone.now();
			scheduleEnd();
			setPlayerState(prev => ({ ...prev, playing: true }));
		}
	}, [clearEndTimeout, scheduleEnd]);

	const seek = useCallback(
		pos => {
			if (!playerRef.current) return;
			clearEndTimeout();
			if (startedAtRef.current !== null) {
				playerRef.current.stop();
				playerRef.current.start(Tone.now(), pos);
				startedAtRef.current = Tone.now();
				startOffsetRef.current = pos;
				scheduleEnd();
			} else {
				startOffsetRef.current = pos;
			}
		},
		[clearEndTimeout, scheduleEnd]
	);

	const setVolume = useCallback(vol => {
		volumeRef.current = vol;
		if (playerRef.current) {
			playerRef.current.volume.value = Tone.gainToDb(vol);
		}
		setPlayerState(prev => ({ ...prev, volume: vol }));
	}, []);

	const setRate = useCallback(
		rate => {
			if (playerRef.current) {
				if (startedAtRef.current !== null) {
					// Snap the tracked offset to the current position before changing rate
					// so future position calculations use the right baseline.
					const elapsed = (Tone.now() - startedAtRef.current) * rateRef.current;
					startOffsetRef.current = startOffsetRef.current + elapsed;
					startedAtRef.current = Tone.now();
				}
				playerRef.current.playbackRate = rate;
			}
			rateRef.current = rate;
			// Counteract the pitch shift caused by speed change: rate r shifts pitch by log₂(r) octaves.
			getPitchShift().pitch = -Math.log2(rate) * 12;
			scheduleEnd();
			setPlayerState(prev => ({ ...prev, rate }));
		},
		[scheduleEnd, getPitchShift]
	);

	const setShowFullscreen = useCallback(value => {
		setPlayerState(prev => ({ ...prev, showFullscreen: value }));
	}, []);

	const player = {
		...playerState,
		play,
		pause,
		togglePlayPause,
		stop,
		seek,
		setVolume,
		setRate,
		setShowFullscreen,
		getPosition,
	};

	const [showImpersonateModal, setShowImpersonateModal] = useState(false);

	usePromptBeforeUnload(player.fileName || player.showFullscreen);

	const isMobile = useMediaQuery({ query: '(max-width: 1023px)' });

	const { can, userChoirs, errors, flash, tenant, navigation } = usePage().props;

	const navFiltered = navigation
		.filter(item => can[item.can])
		.map(item => {
			item.active = item.showAsActiveForRoutes.some(routeName => route().current(routeName));
			item.items = item.items
				.filter(subItem => can[subItem.can])
				.map(subItem => {
					subItem.active = subItem.showAsActiveForRoutes.some(routeName => route().current(routeName));
					return subItem;
				});
			return item;
		});

	return (
		<PlayerContext.Provider value={player}>
			<div className="h-screen flex overflow-hidden bg-gray-100">
				{isMobile ? (
					<SidebarMobile navigation={navFiltered} open={sidebarOpen} setOpen={setSidebarOpen} />
				) : (
					<div className="flex shrink-0">
						<SidebarDesktop navigation={navFiltered} />
					</div>
				)}

				<div className="flex flex-col w-0 flex-1 overflow-hidden">
					{player.showFullscreen || (
						<LayoutTopBar
							setSidebarOpen={setSidebarOpen}
							setShowImpersonateModal={setShowImpersonateModal}
							switchChoirMenu={<SwitchChoirMenu choirs={userChoirs} tenant={tenant} />}
						/>
					)}

					<main
						className="flex-1 flex flex-col justify-stretch relative overflow-y-auto focus:outline-none"
						scroll-region="true"
					>
						{tenant.id === 'demo' && (
							<TenantNotice variant="warning">This demo site is cleared once per week.</TenantNotice>
						)}

						{process.env.MIX_FEATURE_BILLING &&
							tenant.id !== 'demo' &&
							(can.manage_finances || can.update_tenant) && (
								<BillingNotices billing={tenant.billing_status} tenantId={tenant.id} />
							)}

						<ErrorBoundary fallback={() => <OuterPageErrorFallback />} key={route().current()}>
							{children}
						</ErrorBoundary>
					</main>

					{player.fileName && (
						<GlobalTrackPlayer
							songTitle={player.songTitle}
							songId={player.songId}
							fileName={player.fileName}
							close={player.stop}
						/>
					)}
				</div>

				<ToastFlash errors={errors} flash={flash} />

				<ImpersonateUserModal isOpen={showImpersonateModal} setIsOpen={setShowImpersonateModal} />
			</div>
		</PlayerContext.Provider>
	);
}
