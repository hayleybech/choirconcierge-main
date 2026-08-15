import React, { useState, useRef, useCallback, useMemo } from 'react';
import SidebarDesktop from '../components/SidebarDesktop';
import SidebarMobile from '../components/SidebarMobile';
import { usePage } from '@inertiajs/react';
import GlobalTrackPlayer from '../components/Audio/GlobalTrackPlayer';
import { PlayerContext } from '../contexts/player-context';
import { PitchShifter } from 'soundtouchjs';
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
		pan: 0,
		rate: 100,
		pitch: 0,
		showFullscreen: false,
	});

	const audioCtxRef = useRef(null);    // native AudioContext, created once on first play
	const shifterRef = useRef(null);     // PitchShifter instance (SoundTouch wrapper)
	const gainNodeRef = useRef(null);    // GainNode for volume
	const panNodeRef = useRef(null);
	const bufferRef = useRef(null);      // decoded AudioBuffer (needed for duration and seek math)
	const isPlayingRef = useRef(false);  // true while audio is audible (false when paused or stopped)
	const pausedTimeRef = useRef(0);     // timePlayed snapshot taken at pause, used for resume/display
	const rateRef = useRef(100);           // current playback rate, applied to new PitchShifter on load
	const pitchRef = useRef(0);          // current pitch offset in semitones, applied to new PitchShifter on load
	const volumeRef = useRef(1);         // current volume (0–1)
	const panRef = useRef(0);         // current pan (-1 to 1)
	const playIdRef = useRef(0);         // incremented on each play() call to cancel stale async loads
	const abortCtrlRef = useRef(null);   // AbortController for the in-flight fetch

	const getAudioContext = useCallback(async () => {
		if (!audioCtxRef.current) {
			audioCtxRef.current = new AudioContext();
		}
		if (audioCtxRef.current.state === 'suspended') {
			await audioCtxRef.current.resume();
		}
		return audioCtxRef.current;
	}, []);

	const getPosition = useCallback(() => {
		if (isPlayingRef.current && shifterRef.current) {
			return shifterRef.current.timePlayed;
		}
		return pausedTimeRef.current;
	}, []);

	// Disconnect and nullify the current shifter + gain node.
	const teardown = useCallback(() => {
		isPlayingRef.current = false;
		if (shifterRef.current) {
			shifterRef.current.node.disconnect();
			shifterRef.current = null;
		}
		if (gainNodeRef.current) {
			gainNodeRef.current.disconnect();
			gainNodeRef.current = null;
		}
		if(panNodeRef.current) {
			panNodeRef.current.disconnect();
			panNodeRef.current = null;
		}
		bufferRef.current = null;
		pausedTimeRef.current = 0;
	}, []);

	// Silence audio and snapshot the playhead position.
	const doPause = useCallback(() => {
		isPlayingRef.current = false;
		pausedTimeRef.current = shifterRef.current.timePlayed;
		gainNodeRef.current.gain.value = 0;
	}, []);

	const stop = useCallback(() => {
		teardown();
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
	}, [teardown]);

	const play = useCallback(async attachment => {
		const playId = ++playIdRef.current;

		if (abortCtrlRef.current) {
			abortCtrlRef.current.abort();
		}
		const abortCtrl = new AbortController();
		abortCtrlRef.current = abortCtrl;

		teardown();

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

		const audioContext = await getAudioContext();

		let arrayBuffer;
		try {
			const response = await fetch(src, { signal: abortCtrl.signal });
			arrayBuffer = await response.arrayBuffer();
		} catch {
			if (playId === playIdRef.current) setPlayerState(prev => ({ ...prev, loading: false }));
			return;
		}
		if (playId !== playIdRef.current) return;

		let audioBuffer;
		try {
			audioBuffer = await audioContext.decodeAudioData(arrayBuffer);
		} catch {
			if (playId === playIdRef.current) setPlayerState(prev => ({ ...prev, loading: false }));
			return;
		}
		if (playId !== playIdRef.current) return;

		bufferRef.current = audioBuffer;

		const onEnd = () => {
			if (!isPlayingRef.current) return;
			isPlayingRef.current = false;
			pausedTimeRef.current = 0;
			setPlayerState(prev => ({ ...prev, playing: false }));
		};

		const shifter = new PitchShifter(audioContext, audioBuffer, 4096, onEnd);
		shifter.tempo = rateRef.current / 100;
		shifter.pitch = Math.pow(2, pitchRef.current / 12);
		shifterRef.current = shifter;

		const gainNode = audioContext.createGain();
		gainNode.gain.value = volumeRef.current;
		gainNodeRef.current = gainNode;
		shifter.node.connect(gainNode);

		const panner = audioContext.createStereoPanner();
		panner.pan.value = panRef.current;
		panNodeRef.current = panner;
		gainNode.connect(panner);

		panner.connect(audioContext.destination);

		isPlayingRef.current = true;
		setPlayerState(prev => ({ ...prev, loading: false, playing: true, duration: audioBuffer.duration }));
	}, [teardown, getAudioContext]);

	const pause = useCallback(() => {
		if (!shifterRef.current || !isPlayingRef.current) return;
		doPause();
		setPlayerState(prev => ({ ...prev, playing: false }));
	}, [doPause]);

	const togglePlayPause = useCallback(() => {
		if (!shifterRef.current) return;
		if (isPlayingRef.current) {
			doPause();
			setPlayerState(prev => ({ ...prev, playing: false }));
		} else {
			shifterRef.current.percentagePlayed = pausedTimeRef.current / bufferRef.current.duration;
			gainNodeRef.current.gain.value = volumeRef.current;
			panNodeRef.current.pan.value = panRef.current;
			isPlayingRef.current = true;
			setPlayerState(prev => ({ ...prev, playing: true }));
		}
	}, [doPause]);

	const seek = useCallback(pos => {
		if (!shifterRef.current || !bufferRef.current) return;
		shifterRef.current.percentagePlayed = pos / bufferRef.current.duration;
		if (!isPlayingRef.current) {
			pausedTimeRef.current = pos;
		}
	}, []);

	const setVolume = useCallback(vol => {
		volumeRef.current = vol;
		if (gainNodeRef.current && isPlayingRef.current) {
			gainNodeRef.current.gain.value = vol;
		}
		setPlayerState(prev => ({ ...prev, volume: vol }));
	}, []);

	const setPan = useCallback(pan => {
		panRef.current = pan;
		if (panNodeRef.current && isPlayingRef.current) {
			panNodeRef.current.pan.value = pan;
			console.log('pan', pan);
		}
		setPlayerState(prev => ({ ...prev, pan }));
	}, []);

	const setRate = useCallback(rate => {
		rateRef.current = rate;
		if (shifterRef.current) {
			shifterRef.current.tempo = rate / 100;
		}
		setPlayerState(prev => ({ ...prev, rate }));
	}, []);

	const setPitch = useCallback(semitones => {
		pitchRef.current = semitones;
		if (shifterRef.current) {
			shifterRef.current.pitch = Math.pow(2, semitones / 12);
		}
		setPlayerState(prev => ({ ...prev, pitch: semitones }));
	}, []);

	const setShowFullscreen = useCallback(value => {
		setPlayerState(prev => ({ ...prev, showFullscreen: value }));
	}, []);

	const player = useMemo(() => ({
		...playerState,
		play,
		pause,
		togglePlayPause,
		stop,
		seek,
		setVolume,
		setPan,
		setRate,
		setPitch,
		setShowFullscreen,
		getPosition,
	}), [playerState, play, pause, togglePlayPause, stop, seek, setVolume, setPan, setRate, setPitch, setShowFullscreen, getPosition]);

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
