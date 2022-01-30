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
import SwitchChoirModal from "../components/SwitchChoirModal";
import Button from "../components/inputs/Button";
import Icon from "../components/Icon";

export default function Layout({ children }) {
    const [sidebarOpen, setSidebarOpen] = useState(false);
    const [player, setPlayer] = useState({
        songTitle: null,
        songId: 0,
        fileName: null,
        src: null,
        play: play,

        showFullscreen: false,
        setShowFullscreen: setFullscreen,
    });
    const [showImpersonateModal, setShowImpersonateModal] = useState(false);
    const [showSwitchChoirModal, setShowSwitchChoirModal] = useState(false);

    const { can, userChoirs } = usePage().props;

    function play(attachment) {
        setPlayer(oldState => ({
            ...oldState,
            songTitle: attachment.song.title,
            songId: attachment.song.id,
            fileName: attachment.title !== '' ? attachment.title : attachment.filepath,
            src: attachment.download_url,
        }));
    }

    function setFullscreen(value) {
        setPlayer(oldState => ({
            ...oldState,
            showFullscreen: value,
        }));
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
                <SidebarMobile
                    navigation={navFiltered}
                    open={sidebarOpen}
                    setOpen={setSidebarOpen}
                    switchChoirButton={userChoirs.length > 1 && <SwitchChoirButton onClick={() => { setSidebarOpen(false); setShowSwitchChoirModal(true); }} />}
                />

                {/* Static sidebar for desktop */}
                <div className="hidden xl:flex xl:flex-shrink-0">
                    <SidebarDesktop
                        navigation={navFiltered}
                        switchChoirButton={userChoirs.length > 1 && <SwitchChoirButton onClick={() => setShowSwitchChoirModal(true)}/>}
                    />
                </div>
                <div className="flex flex-col w-0 flex-1 overflow-hidden">
                    {player.showFullscreen || <LayoutTopBar setSidebarOpen={setSidebarOpen} setShowImpersonateModal={setShowImpersonateModal} />}

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
                <SwitchChoirModal setIsOpen={setShowSwitchChoirModal} isOpen={showSwitchChoirModal} choirs={userChoirs} />
            </div>
        </PlayerContext.Provider>
    )
}

const SwitchChoirButton = ({ onClick }) => (
    <Button variant="secondary" size="xs" className="mx-4" onClick={onClick}>
        <Icon icon="exchange" mr />
        Switch Choir
    </Button>
);