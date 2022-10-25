import PitchButton from "./PitchButton";
import React from "react";

const pitches = ['C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B'];

const PitchScale = ({ root, synth }) => (
    <>
        <PitchButton synth={synth} note={root} size="sm" className="h-7" />

        {rotate(pitches, pitches.indexOf(root)).slice(1).map((pitch) => (
            <PitchButton synth={synth} note={pitch} withIcon={false} variant="secondary" size="sm" className="h-7" key={pitch} />
        ))}
    </>
);

export default PitchScale;

const rotate = (arr, n = 1) => [...arr.slice(n, arr.length), ...arr.slice(0, n)]