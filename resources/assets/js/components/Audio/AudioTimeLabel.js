import React, { useContext } from 'react';
import { PlayerContext } from "../../contexts/player-context";

const formatTime = (seconds) => {
    if (isNaN(seconds) || seconds === null) return "0:00";
    const floored = Math.floor(seconds);
    let from = 14;
    let length = 5;
    // Display hours only if necessary.
    if (floored >= 3600) {
        from = 11;
        length = 8;
    }

    return new Date(floored * 1000).toISOString().substr(from, length);
}

export const AudioTimeLabel = ({ show = 'both' }) => {
    const player = useContext(PlayerContext);

    if (player.duration === Infinity || player.duration === 0) return null;
    const elapsed = typeof player.position === "number" ? player.position : 0;

    return (
        <div className="text-gray-500 text-xs">
            {{
                'elapsed': formatTime(elapsed),
                'length': formatTime(player.duration),
                'both': `${formatTime(elapsed)}/${formatTime(player.duration)}`,
            }[show]}
        </div>
    );
}