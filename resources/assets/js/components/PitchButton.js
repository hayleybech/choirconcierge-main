import React, {useState} from 'react';
import Button from "./inputs/Button";
import {start, Synth} from "tone";

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
        <Button onMouseDown={play} onMouseUp={stop} variant="primary" size={size}>
            <i className="fa fa-play mr-1" />
            <span className="key w-4">{note}</span>
        </Button>
    );
}

export default PitchButton;