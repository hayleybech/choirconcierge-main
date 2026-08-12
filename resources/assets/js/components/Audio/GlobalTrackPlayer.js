import React, { useCallback, useContext, useEffect } from 'react';

import Button from "../inputs/Button";
import { PlayerContext } from '../../contexts/player-context';
import {AudioTimeLabel} from "./AudioTimeLabel";
import {AudioSeekBar} from "./AudioSeekBar";
import {AudioVolumeButton} from "./AudioVolumeButton";
import Icon from "../Icon";
import {Link} from "@inertiajs/react";
import LoadingSpinner from "../LoadingSpinner";
import useRoute from "../../hooks/useRoute";

const GlobalTrackPlayer = ({ songTitle, songId, fileName, close, howlRef }) => {
    const { route } = useRoute();
    const player = useContext(PlayerContext);

	const intervalRef = React.useRef(null);

	const [position, setPosition] = React.useState(0);

	const updatePosition = useCallback(() => {
		if (howlRef.current) {
			const currentPos = howlRef.current.seek();
			if (typeof currentPos === 'number') {
				setPosition(currentPos);
			}
		}
	}, []);

	useEffect(() => {
		if (player.playing) {
			intervalRef.current = setInterval(updatePosition, 500);
		} else {
			clearInterval(intervalRef.current);
		}
		return () => clearInterval(intervalRef.current);
	}, [player.playing, updatePosition]);

    return (
        <div className="relative z-10 shrink-0 h-auto sm:h-12 bg-white border-t border-gray-300 flex flex-col sm:flex-row items-center justify-between py-2 px-2 sm:pl-6">
            <div className="flex flex-row items-start w-full min-w-0">
                <Button variant="clear" size="xs" disabled className="invisible sm:hidden shrink-0">
                    <Icon icon="times" />
                </Button>

                <div className="flex flex-col sm:flex-row items-center text-center sm:text-left mb-2 sm:mb-0 sm:mr-8 min-w-0 grow">
                    <Link href={route('songs.show', {song: songId})} className="text-sm text-purple-800 sm:mr-4 w-full truncate">{songTitle}</Link>
                    <span className="text-gray-600 text-xs truncate w-full">{fileName}</span>
                </div>

                <Button variant="clear" size="xs" onClick={() => {player.stop(); close();}} className="sm:hidden shrink-0">
                    <Icon icon="times" />
                </Button>
            </div>
            <div className="flex items-center space-x-2 grow w-full sm:w-auto">
                {player.loading && <LoadingSpinner />}
                <Button variant="clear" size="xs" onClick={player.togglePlayPause} disabled={player.loading}>
                    {player.playing ? <Icon icon="pause" /> : <Icon icon="play" />}
                </Button>
                <div className="flex items-center space-x-1.5 grow">
                    <AudioTimeLabel show="elapsed" position={position} />
                    <AudioSeekBar position={position} />
                    <AudioTimeLabel show="length" position={position} />
                </div>
                <AudioVolumeButton />

                <Button variant="clear" size="xs" onClick={() => {player.stop(); close();}} className="hidden sm:inline-flex -mr-2">
                    <Icon icon="times" />
                </Button>
            </div>
        </div>
    );
}

export default GlobalTrackPlayer;