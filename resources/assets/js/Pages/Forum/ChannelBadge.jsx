import React from 'react';
import Badge from '../../components/Badge';

const CHANNEL_COLOURS = [
	'bg-purple-100 text-purple-800',
	'bg-blue-100 text-blue-800',
	'bg-emerald-100 text-emerald-800',
	'bg-amber-100 text-amber-800',
	'bg-rose-100 text-rose-800',
	'bg-cyan-100 text-cyan-800',
	'bg-indigo-100 text-indigo-800',
	'bg-orange-100 text-orange-800',
];

export const ChannelName = ({ name }) => (
	<span className="inline-flex items-center gap-1">
		<span aria-hidden="true">#</span>
		<span>{name}</span>
	</span>
);

const ChannelBadge = ({ name, colourIndex = 0 }) => {
	const safeColourIndex = Math.max(0, colourIndex) % CHANNEL_COLOURS.length;

	return (
		<Badge colour={CHANNEL_COLOURS[safeColourIndex]}>
			<ChannelName name={name} />
		</Badge>
	);
};

export default ChannelBadge;
