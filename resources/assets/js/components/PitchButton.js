import React, { useRef } from 'react';
import Button from "./inputs/Button";
import { start } from "tone";
import Icon from "./Icon";

const PitchButton = ({ instrument, note, octave = 4, withIcon = true, variant = "primary", size = "md", className, labelClassName = 'w-4', ...props }) => {
    const pitch = (note.length > 1 ? note.slice(0, 2) : note) + octave.toString();
    const releaseHandlerRef = useRef(null);

    function play(e) {
        e.stopPropagation();
        e.preventDefault();

        // Define and capture the release handler so we can remove the exact same reference later,
        // even if the component re-renders between press and release.
        function handleRelease(evt) {
            evt.stopPropagation();
            evt.preventDefault();
            document.removeEventListener('mouseup', handleRelease);
            document.removeEventListener('touchend', handleRelease);
            releaseHandlerRef.current = null;
            instrument.instrument.triggerRelease();
        }

        releaseHandlerRef.current = handleRelease;
        document.addEventListener('mouseup', handleRelease);
        document.addEventListener('touchend', handleRelease);
        start();
        instrument.instrument.triggerAttack(pitch);
    }

    function stop(e) {
        e.stopPropagation();
        e.preventDefault();
        // Remove the document listener that was set in play(), then release the note.
        if (releaseHandlerRef.current) {
            document.removeEventListener('mouseup', releaseHandlerRef.current);
            document.removeEventListener('touchend', releaseHandlerRef.current);
            releaseHandlerRef.current = null;
        }
        instrument.instrument.triggerRelease();
    }

    return (
        <Button onMouseDown={play} onMouseUp={stop} onTouchStart={play} onTouchEnd={stop} variant={variant} size={size} className={className} {...props}>
            {withIcon && <Icon icon="play" />}
            <span className={labelClassName}>{note}</span>
        </Button>
    );
};

export default PitchButton;
