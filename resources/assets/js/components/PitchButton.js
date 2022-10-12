import React, {useEffect, useState} from 'react';
import Button from "./inputs/Button";
import {start} from "tone";
import Icon from "./Icon";

const PitchButton = ({ synth, note, octave = 4, withIcon = true, variant="primary", size = "md", className }) => {
    const [pitch] = useState(note + octave.toString());

    function play(e) {
        e.stopPropagation();
        e.preventDefault();

        start();
        synth.envelope.release = 0.3;
        synth.envelope.sustain = 0.1;
        synth.volume.value = 50;
        synth.triggerAttack(pitch);
    }

    function stop(e) {
        e.stopPropagation();
        e.preventDefault();

        synth.triggerRelease();
    }

    useEffect(() => {
        document.addEventListener('mouseup', stop);
        document.addEventListener('touchend', stop);
        return () => {
            document.removeEventListener('mouseup', stop);
            document.removeEventListener('touchend', stop);
        }
    }, []);

    return (
        <Button onMouseDown={play} onMouseUp={stop} onTouchStart={play} onTouchEnd={stop} variant={variant} size={size} className={className}>
            {withIcon && <Icon icon="play" />}
            <span className="key w-4">{note}</span>
        </Button>
    );
}

export default PitchButton;