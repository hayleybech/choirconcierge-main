import React from 'react';
import Button from "../inputs/Button";
import { useAudioPlayer } from "react-use-audio-player"
import {AudioTimeLabel} from "./AudioTimeLabel";
import {AudioSeekBar} from "./AudioSeekBar";
import {AudioVolumeButton} from "./AudioVolumeButton";
import Icon from "../Icon";
import {Link} from "@inertiajs/inertia-react";
import LoadingSpinner from "../LoadingSpinner";

const GlobalTrackPlayer = ({ songTitle, songId, fileName, close }) => {
    const { togglePlayPause, ready, loading, playing, stop } = useAudioPlayer();

    return (
        <div className="relative z-10 shrink-0 flex h-auto sm:h-12 bg-white border-t border-gray-300 flex flex-col sm:flex-row items-center justify-between py-2 px-2 sm:pl-6">
            <div className="flex flex-row items-start">
                <Button variant="clear" size="xs" disabled className="invisible sm:hidden">
                    <Icon icon="times" />
                </Button>

                <div className="flex flex-col sm:flex-row items-center text-center sm:text-left mb-2 sm:mb-0 sm:mr-8">
                    <Link href={route('songs.show', songId)} className="text-sm text-purple-800 sm:mr-4">{songTitle}</Link>
                    <span className="text-gray-600 text-xs truncate">{fileName}</span>
                </div>

                <Button variant="clear" size="xs" onClick={() => {stop(); close();}} className="sm:hidden">
                    <Icon icon="times" />
                </Button>
            </div>
            <div className="flex items-center space-x-2 grow w-full sm:w-auto">
                {loading && <LoadingSpinner />}
                <Button variant="clear" size="xs" onClick={togglePlayPause} disabled={!ready}>
                    {playing ? <Icon icon="pause" /> : <Icon icon="play" />}
                </Button>
                <div className="flex items-center space-x-1.5 grow">
                    <AudioTimeLabel show="elapsed" />
                    <AudioSeekBar />
                    <AudioTimeLabel show="length" />
                </div>
                <AudioVolumeButton />

                <Button variant="clear" size="xs" onClick={() => {stop(); close();}} className="hidden sm:inline-flex -mr-2">
                    <Icon icon="times" />
                </Button>
            </div>
        </div>
    );
}

export default GlobalTrackPlayer;