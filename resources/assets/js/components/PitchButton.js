import React, {useState} from 'react';
import Button from "./inputs/Button";
import {start} from "tone";
import Icon from "./Icon";

const PitchButton = ({ instrument, note, octave = 4, withIcon = true, variant="primary", size = "md", className, labelClassName = 'w-4', ...props }) => {
    const pitch = (note.length > 1 ? note.slice(0, 2) : note) + octave.toString();

    function play(e) {
        e.stopPropagation();
        e.preventDefault();
        document.addEventListener('mouseup', stop);
        document.addEventListener('touchend', stop);

        start();
        instrument.instrument.triggerAttack(pitch);
    }

    function stop(e) {
        e.stopPropagation();
        e.preventDefault();
        document.removeEventListener('mouseup', stop);
        document.removeEventListener('touchend', stop);

        instrument.instrument.triggerRelease();
    }

    return (
        <Button onMouseDown={play} onMouseUp={stop} onTouchStart={play} onTouchEnd={stop} variant={variant} size={size} className={className} {...props}>
            {withIcon && <Icon icon="play" />}
            <span className={labelClassName}>{note}</span>
        </Button>
    );
}

export default PitchButton;