import React, { useState, useRef, useCallback } from 'react';
import SidebarDesktop from '../components/SidebarDesktop';
import SidebarMobile from '../components/SidebarMobile';
import { usePage } from '@inertiajs/react';
import GlobalTrackPlayer from '../components/Audio/GlobalTrackPlayer';
import { PlayerContext } from '../contexts/player-context';
import { Howl } from 'howler';
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

	const howlRef = useRef(null);

	const stop = useCallback(() => {
		if (howlRef.current) {
			howlRef.current.stop();
			howlRef.current.unload();
			howlRef.current = null;
		}
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
		// setPosition(0);
	}, []);

	const play = useCallback(
		attachment => {
			if (howlRef.current) {
				howlRef.current.stop();
				howlRef.current.unload();
			}

			const src = attachment.download_url;
			// setPosition(0);
			setPlayerState(prev => ({
				...prev,
				songTitle: attachment.song.title,
				songId: attachment.song.id,
				fileName: attachment.title !== '' ? attachment.title : attachment.filepath,
				src: src,
				loading: true,
				playing: false,
			}));

			howlRef.current = new Howl({
				src: [src],
				volume: playerState.volume,
				onload: () => {
					setPlayerState(prev => ({
						...prev,
						loading: false,
						duration: howlRef.current.duration(),
					}));
				},
				onplay: () => setPlayerState(prev => ({ ...prev, playing: true, loading: false })),
				onpause: () => setPlayerState(prev => ({ ...prev, playing: false })),
				onstop: () => {
					setPlayerState(prev => ({ ...prev, playing: false }));
					// setPosition(0);
				},
				onend: () => {
					setPlayerState(prev => ({ ...prev, playing: false }));
					// setPosition(0);
				},
				onloaderror: () => setPlayerState(prev => ({ ...prev, loading: false })),
				onplayerror: () => {
					howlRef.current.once('unlock', () => howlRef.current.play());
				},
			});

			howlRef.current.play();
		},
		[playerState.volume]
	);

	const pause = useCallback(() => {
		if (howlRef.current) {
			howlRef.current.pause();
		}
	}, []);

	const togglePlayPause = useCallback(() => {
		if (!howlRef.current) return;
		if (howlRef.current.playing()) {
			howlRef.current.pause();
		} else {
			howlRef.current.play();
		}
	}, []);

	const seek = useCallback(pos => {
		if (howlRef.current) {
			howlRef.current.seek(pos);
			// setPosition(pos);
		}
	}, []);

	const setVolume = useCallback(vol => {
		if (howlRef.current) {
			howlRef.current.volume(vol);
		}
		setPlayerState(prev => ({ ...prev, volume: vol }));
	}, []);

	const setRate = useCallback(rate => {
		if (howlRef.current) {
			howlRef.current.rate(rate);
		}
		setPlayerState(prev => ({ ...prev, rate }));
	}, []);

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
							howlRef={howlRef}
						/>
					)}
				</div>

				<ToastFlash errors={errors} flash={flash} />

				<ImpersonateUserModal isOpen={showImpersonateModal} setIsOpen={setShowImpersonateModal} />
			</div>
		</PlayerContext.Provider>
	);
}
