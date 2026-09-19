import PitchButton from './PitchButton';
import React from 'react';

const pitches = ['C', 'C#/Db', 'D', 'D#/Eb', 'E', 'F', 'F#/Gb', 'G', 'G#/Ab', 'A', 'A#/Bb', 'B'];

const PitchScale = ({ instrument }) => (
	<div className="flex flex-wrap p-1.5 gap-x-1 gap-y-1.5 border-b border-gray-300">
		{pitches.map(pitch => (
			<PitchButton
				instrument={instrument}
				note={pitch}
				withIcon={false}
				variant={pitch.length > 1 ? 'dark' : 'secondary'}
				size="xs"
				className="h-7 grow"
				labelClassName="w-10"
				key={pitch}
			/>
		))}
	</div>
);

export default PitchScale;
