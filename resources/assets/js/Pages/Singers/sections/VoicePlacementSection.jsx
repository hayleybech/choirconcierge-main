import CollapsePanel from '../../../components/CollapsePanel';
import { DetailList, DetailListItem } from '../components/DetailList';
import VoicePartTag from '../../../components/VoicePartTag';
import Icon from '../../../components/Icon';
import { Progress } from '../components/Progress';
import React from 'react';
import { Range } from '../components/Range';
import useRoute from '../../../hooks/useRoute';
import ButtonLink from '../../../components/inputs/ButtonLink';

export const VoicePlacementSection = ({ singer }) =>
	singer.placement ? (
		<CollapsePanel>
			<DetailList gridCols="sm:grid-cols-2">
				<DetailListItem label="Voice Part">
					<VoicePartTag title={singer.voice_part.title} colour={singer.voice_part.colour} />
				</DetailListItem>
				<DetailListItem label="Voice Tone">
					<Range
						min={1}
						max={3}
						minLabel={<Icon icon="flute" className="text-gray-600 fa-lg" />}
						maxLabel={<Icon icon="trumpet" className="text-gray-600 fa-lg" />}
						value={singer.placement.voice_tone}
					/>
				</DetailListItem>
				<DetailListItem label="Pitch Skill">
					<Progress value={singer.placement.skill_pitch} min={1} max={5} />
				</DetailListItem>
				<DetailListItem label="Harmony Skill">
					<Progress value={singer.placement.skill_harmony} min={1} max={5} />
				</DetailListItem>
				<DetailListItem label="Performance Skill">
					<Progress value={singer.placement.skill_performance} min={1} max={5} />
				</DetailListItem>
				<DetailListItem label="Sight Reading Skill">
					<Progress value={singer.placement.skill_sightreading} min={1} max={5} />
				</DetailListItem>
				<DetailListItem label="Experience">{singer.placement.experience ?? 'None listed'}</DetailListItem>
				<DetailListItem label="Instruments">{singer.placement.instruments ?? 'None listed'}</DetailListItem>
			</DetailList>
		</CollapsePanel>
	) : (
		<VoicePlacementEmptyState singer={singer} />
	);

const VoicePlacementEmptyState = ({ singer }) => {
	const { route } = useRoute();

	return (
		<div className="text-center py-6 px-4">
			<Icon icon="user-music" type="light" className="text-gray-400 text-4xl mb-2" />
			<h3 className="mt-2 text-sm font-medium text-gray-900">No Voice Placement</h3>
			<p className="mt-1 text-sm text-gray-500">
				Get this singer started on their journey by creating their Voice Placement.
			</p>
			<div className="mt-6">
				<ButtonLink href={route('singers.placements.create', { singer })} variant="primary">
					<Icon icon="plus" />
					Create Placement
				</ButtonLink>
			</div>
		</div>
	);
};
