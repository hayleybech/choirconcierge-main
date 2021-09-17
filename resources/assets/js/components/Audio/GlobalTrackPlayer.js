import React from 'react';
import Button from "../inputs/Button";
import { useAudioPlayer } from "react-use-audio-player"
import {AudioTimeLabel} from "./AudioTimeLabel";
import {AudioSeekBar} from "./AudioSeekBar";
import {AudioVolumeButton} from "./AudioVolumeButton";

const LoadingSpinner = () => (
    <i className="fad fa-spinner fa-pulse text-xl text-purple-500" />
);

const GlobalTrackPlayer = ({ songTitle, fileName }) => {
    const { togglePlayPause, ready, loading, playing } = useAudioPlayer();

    return (
        <div className="relative z-10 flex-shrink-0 flex h-auto sm:h-12 bg-white border-t border-gray-300 flex flex-col sm:flex-row items-center justify-between py-2 px-4 sm:px-8">
            <div className="flex flex-col sm:flex-row items-center text-center sm:text-left mb-2 sm:mb-0 sm:mr-8">
                <span className="text-gray-800 text-sm mr-4">{songTitle}</span>
                <span className="text-gray-600 text-xs truncate">{fileName}</span>
            </div>
            <div className="flex items-center space-x-2 flex-grow w-full sm:w-auto">
                {loading && <LoadingSpinner />}
                <Button variant="clear" size="xs" onClick={togglePlayPause} disabled={!ready}>
                    {playing ? <i className="fas fa-fw fa-pause" /> : <i className="fas fa-fw fa-play" />}
                </Button>
                <div className="flex items-center space-x-1.5 flex-grow">
                    <AudioTimeLabel show="elapsed" />
                    <AudioSeekBar />
                    <AudioTimeLabel show="length" />
                </div>
                <AudioVolumeButton />
            </div>
        </div>
    );
}

export default GlobalTrackPlayer;