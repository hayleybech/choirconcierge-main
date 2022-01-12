import React, {useState} from 'react';
import Button from "./inputs/Button";
import {start, Synth} from "tone";
import Icon from "./Icon";

const PitchButton = ({ note, octave = 4, size = "md"}) => {
    const [synth] = useState(new Synth().toDestination());
    const [pitch] = useState(note + octave.toString());

    function play(e) {
        e.stopPropagation();
        e.preventDefault();
        document.addEventListener('mouseup', stop);
        document.addEventListener('touchend', stop);

        start();
        synth.triggerAttack(pitch);
    }

    function stop(e) {
        e.stopPropagation();
        e.preventDefault();
        document.removeEventListener('mouseup', stop);
        document.removeEventListener('touchend', stop);

        synth.triggerRelease();
    }

    return (
        <Button onMouseDown={play} onMouseUp={stop} onTouchStart={play} onTouchEnd={stop} variant="primary" size={size}>
            <Icon icon="play" mr />
            <span className="key w-4">{note}</span>
        </Button>
    );
}

export default PitchButton;