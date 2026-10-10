import PitchButton from './PitchButton';
import React from 'react';

const pitches = ['C', 'C#/Db', 'D', 'D#/Eb', 'E', 'F', 'F#/Gb', 'G', 'G#/Ab', 'A', 'A#/Bb', 'B'];

const PitchScale = ({ instrument }) => (
	<div className="flex flex-wrap p-1.5 gap-x-1 gap-y-1.5 border-b border-gray-300 bg-gray-50">
		{pitches.map(pitch => (
			<PitchButton
				instrument={instrument}
				note={pitch}
				withIcon={false}
				colour={pitch.length > 1 ? 'secondary' : 'secondary'}
				theme={pitch.length > 1 ? 'dark' : 'light'}
				weight="clear"
				size="sm"
				className={`h-7 grow ${pitch.length > 1 ? 'bg-gray-800' : 'bg-white'}`}
				labelClassName="w-10"
				key={pitch}
			/>
		))}
	</div>
);

export default PitchScale;
