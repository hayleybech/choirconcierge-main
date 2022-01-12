import React, { useState } from 'react'
import route from 'ziggy-js';
import SidebarDesktop from "../components/SidebarDesktop";
import SidebarMobile from "../components/SidebarMobile";
import navigation from "./navigation";
import {usePage} from '@inertiajs/inertia-react';
import GlobalTrackPlayer from "../components/Audio/GlobalTrackPlayer";
import { PlayerContext } from '../contexts/player-context';
import { AudioPlayerProvider } from "react-use-audio-player"
import ImpersonateUserModal from "../components/ImpersonateUserModal";
import LayoutTopBar from "../components/LayoutTopBar";

export default function Layout({children}) {
    const [sidebarOpen, setSidebarOpen] = useState(false);
    const [player, setPlayer] = useState({
        songTitle: null,
        songId: 0,
        fileName: null,
        src: null,
        play: play,
    });
    const [showImpersonateModal, setShowImpersonateModal] = useState(false);

    const { can } = usePage().props;

    function play(attachment) {
        setPlayer({
            ...player,
            songTitle: attachment.song.title,
            songId: attachment.song.id,
            fileName: attachment.title !== '' ? attachment.title : attachment.filepath,
            src: attachment.download_url,
        });
    }

    const navFiltered = navigation
        .filter((item) => can[item.can])
        .map((item) => {
            item.active = item.showAsActiveForRoutes.some((routeName) => route().current(routeName));
            item.items.map((subItem) => {
                subItem.active = subItem.showAsActiveForRoutes.some((routeName) => route().current(routeName));
                return subItem;
            })
            return item;
        });

    return (
        <PlayerContext.Provider value={player}>
            <div className="h-screen flex overflow-hidden bg-gray-100">
                <SidebarMobile navigation={navFiltered} open={sidebarOpen} setOpen={setSidebarOpen} />

                {/* Static sidebar for desktop */}
                <div className="hidden xl:flex xl:flex-shrink-0">
                    <SidebarDesktop navigation={navFiltered} />
                </div>
                <div className="flex flex-col w-0 flex-1 overflow-hidden">
                    <LayoutTopBar setSidebarOpen={setSidebarOpen} setShowImpersonateModal={setShowImpersonateModal} />

                    <AudioPlayerProvider>
                        <main className="flex-1 flex flex-col justify-stretch relative overflow-y-auto focus:outline-none">
                            {children}
                        </main>

                        {player.fileName &&
                            <GlobalTrackPlayer
                                songTitle={player.songTitle}
                                songId={player.songId}
                                fileName={player.fileName}
                                close={() => setPlayer({
                                    ...player,
                                    songTitle: null,
                                    songId: 0,
                                    fileName: null,
                                    src: null,
                                })}
                            />
                        }
                    </AudioPlayerProvider>
                </div>

                <ImpersonateUserModal isOpen={showImpersonateModal} setIsOpen={setShowImpersonateModal} />
            </div>
        </PlayerContext.Provider>
    )
}
