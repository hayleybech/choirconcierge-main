import React, {useState} from 'react';
import Button from "./inputs/Button";
import {start, Synth} from "tone";
import Icon from "./Icon";

const PitchButton = ({ note, octave = 4, withIcon = true, variant="primary", size = "md", className }) => {
    const [synth] = useState(new Synth().toDestination());
    const [pitch] = useState(note + octave.toString());

    function play(e) {
        e.stopPropagation();
        e.preventDefault();
        document.addEventListener('mouseup', stop);
        document.addEventListener('touchend', stop);

        start();
        synth.envelope.release = 0.3;
        synth.envelope.sustain = 0.1;
        synth.volume.value = 50;
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
        <Button onMouseDown={play} onMouseUp={stop} onTouchStart={play} onTouchEnd={stop} variant={variant} size={size} className={className}>
            {withIcon && <Icon icon="play" />}
            <span className="key w-4">{note}</span>
        </Button>
    );
}

export default PitchButton;