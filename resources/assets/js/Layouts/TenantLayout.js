import React, {useState} from 'react'
import SidebarDesktop from "../components/SidebarDesktop";
import SidebarMobile from "../components/SidebarMobile";
import navigation from "./navigation";
import {usePage} from '@inertiajs/inertia-react';
import GlobalTrackPlayer from "../components/Audio/GlobalTrackPlayer";
import { PlayerContext } from '../contexts/player-context';
import { AudioPlayerProvider } from "react-use-audio-player"
import ImpersonateUserModal from "../components/ImpersonateUserModal";
import LayoutTopBar from "../components/LayoutTopBar";
import ToastFlash from "../components/ToastFlash";
import {useMediaQuery} from "react-responsive";
import usePromptBeforeUnload from "../hooks/usePromptBeforeUnload";
import useRoute from "../hooks/useRoute";
import SwitchChoirMenu from "../components/SwitchChoirMenu";

export default function TenantLayout({ children }) {
    const [sidebarOpen, setSidebarOpen] = useState(false);
    const { route } = useRoute();

    const [player, setPlayer] = useState({
        songTitle: null,
        songId: 0,
        fileName: null,
        src: null,
        play: (attachment) => setPlayer(oldState => ({
            ...oldState,
            songTitle: attachment.song.title,
            songId: attachment.song.id,
            fileName: attachment.title !== '' ? attachment.title : attachment.filepath,
            src: attachment.download_url,
        })),
        stop: () => setPlayer({
            ...player,
            songTitle: null,
            songId: 0,
            fileName: null,
            src: null,
        }),

        showFullscreen: false,
        setShowFullscreen: (value) => setPlayer(oldState => ({
            ...oldState,
            showFullscreen: value,
        })),
    });
    const [showImpersonateModal, setShowImpersonateModal] = useState(false);

    usePromptBeforeUnload(player.fileName || player.showFullscreen);

    const isMobile = useMediaQuery({ query: '(max-width: 1023px)' });

    const { can, userChoirs, errors, flash, tenant } = usePage().props;

    const navFiltered = navigation
        .filter((item) => can[item.can])
        .map((item) => {
            item.active = item.showAsActiveForRoutes.some((routeName) => route().current(routeName));
            item.items = item.items
                .filter((subItem) => can[subItem.can])
                .map((subItem) => {
                    subItem.active = subItem.showAsActiveForRoutes.some((routeName) => route().current(routeName));
                    return subItem;
                });
            return item;
        })

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

                    <AudioPlayerProvider>
                        <main className="flex-1 flex flex-col justify-stretch relative overflow-y-auto focus:outline-none" scroll-region="true">
                            {children}
                        </main>

                        {player.fileName &&
                            <GlobalTrackPlayer songTitle={player.songTitle} songId={player.songId} fileName={player.fileName} close={player.stop} />
                        }
                    </AudioPlayerProvider>
                </div>

                <ToastFlash errors={errors} flash={flash} />

                <ImpersonateUserModal isOpen={showImpersonateModal} setIsOpen={setShowImpersonateModal} />
            </div>
        </PlayerContext.Provider>
    )
}