import React, {
    useCallback,
    useEffect,
    useRef,
    useState,
} from "react"
import { useAudioPlayer, useAudioPosition } from "react-use-audio-player"

export const AudioSeekBar = ({ className }) => {
    const { duration, seek, percentComplete } = useAudioPosition({
        highRefreshRate: true
    })
    const { playing } = useAudioPlayer();
    const [barWidth, setBarWidth] = useState("0%");

    const seekBarElem = useRef(null);

    useEffect(() => {
        setBarWidth(`${percentComplete}%`);
    }, [percentComplete]);

    const goTo = useCallback(
        (event) => {
            const { pageX: eventOffsetX } = event;

            if (seekBarElem.current) {
                const elementOffsetX = seekBarElem.current.getBoundingClientRect().left;
                const elementWidth = seekBarElem.current.clientWidth;
                const percent = ((eventOffsetX - elementOffsetX) / elementWidth) * 100;
                seek(percent/100 * duration);
            }
        },
        [duration, playing, seek]
    );

    return (
        <div
            className={`bg-gray-800 cursor-pointer overflow-hidden flex-grow sm:w-64 h-4 rounded ${className}`}
            ref={seekBarElem}
            onClick={goTo}
        >
            <div style={{ width: barWidth }} className="bg-purple-500 h-full" />
        </div>
    )
}