import AttendanceTag from '../../../components/Event/AttendanceTag';

import useRoute from '../../../hooks/useRoute';
import ButtonLink from '../../../components/inputs/ButtonLink';
import Icon from '../../../components/Icon';
import React from 'react';

const events = [
	{ id: 1, name: 'Rehearsal 1', date: '2022-01-01', response: 'present' },
	{ id: 2, name: 'Rehearsal 2', date: '2022-01-02', response: 'absent' },
	{ id: 3, name: 'Rehearsal 3', date: '2022-01-03', response: 'late' },
	{ id: 4, name: 'Rehearsal 4 with a very long title that should be cut off', date: '2022-01-04', response: 'late_deemed_absent' },
	{ id: 5, name: 'Rehearsal 5', date: '2022-01-05', response: 'absent_apology'},
];

const responses = {
	present: {
		label: 'On Time',
		colour: 'emerald',
		icon: 'check',
	},
	late: {
		label: 'Late',
		colour: 'amber',
		icon: 'alarm-exclamation',
	},
	absent: {
		label: 'Absent',
		colour: 'red',
		icon: 'times',
	},
	absent_apology: {
		label: 'Absent (Apology sent)',
		colour: 'red',
		icon: 'times',
	},
	late_deemed_absent: {
		label: 'Absent (Very late)',
		colour: 'red',
		icon: 'times',
	},
	unknown: {
		label: 'Unknown',
		colour: 'gray',
		icon: 'question',
	},
};

export const AttendanceSection = ({ singer }) => {

	return (
		<div className="py-4 px-8">
			<div className="flex justify-between items-center mb-4">
				<div>
					<p>Last 8 rehearsals: 4 present (50%)</p>
					<p>3-month average: 62%</p>
				</div>
			</div>

			<h3 className="mt-4">Records:</h3>
			<ol>
					{events.map(event => (
						<li key={event.id} className="flex justify-between gap-2 text-sm mb-1">
							<span className="flex gap-2">
								<span className="text-gray-500 shrink-0">{event.date}</span>
								<span className="font-bold">{event.name}</span>
							</span>
							<AttendanceTag
								icon={responses[event.response].icon}
								colour={responses[event.response].colour}
								label={responses[event.response].label}
								className="shrink-0"
							/>
						</li>
					))}
				</ol>
			</div>
		);
};

export default AttendanceSection;
