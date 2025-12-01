import React from 'react';
import { useAudioPosition } from "react-use-audio-player"

const formatTime = (seconds) => {
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
    const {duration, position} = useAudioPosition({highRefreshRate: true});
    if (duration === Infinity) return null;
    const elapsed = typeof position === "number" ? position : 0;

    return (
        <div className="text-gray-500 text-xs">
            {{
                'elapsed': formatTime(elapsed),
                'length': formatTime(duration),
                'both': `${formatTime(elapsed)}/${formatTime(duration)}`,
            }[show]}
        </div>
    );
}