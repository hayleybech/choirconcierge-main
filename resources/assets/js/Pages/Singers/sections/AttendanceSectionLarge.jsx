import AttendanceTag from '../../../components/Event/AttendanceTag';
import FilterSortPane from '../../../components/FilterSortPane';
import AttendanceReportFilters from '../../Events/AttendanceReportFilters';
import React from 'react';
import useFilterPane from '../../../hooks/useFilterPane';
import useSortFilterForm from '../../../hooks/useSortFilterForm';
import Button from '../../../components/inputs/Button';
import Icon from '../../../components/Icon';

const events = [
	{ id: 1, name: 'Rehearsal 1', date: '2022-01-01', response: 'present' },
	{ id: 2, name: 'Rehearsal 2', date: '2022-01-02', response: 'absent' },
	{ id: 3, name: 'Rehearsal 3', date: '2022-01-03', response: 'late' },
	{
		id: 4,
		name: 'Rehearsal 4 with a very long title that should be cut off',
		date: '2022-01-04',
		response: 'late_deemed_absent',
	},
	{ id: 5, name: 'Rehearsal 5', date: '2022-01-05', response: 'absent_apology' },
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

export const AttendanceSectionLarge = () => {
	const [showFilters, setShowFilters, filterAction, hasNonDefaultFilters] = useFilterPane();

	const sorts = [];
	const filters = [
		{ name: 'type.id', multiple: true, defaultValue: [] },
		{ name: 'starts_after' },
		{ name: 'starts_before' },
	];

	const sortFilterForm = useSortFilterForm('events.reports.attendance', filters, sorts);

	return (
		<div className="flex flex-col overflow-auto lg:flex-row divide-y lg:divide-y-0 lg:divide-x divide-gray-300 h-full">
			{showFilters && (
				<div className="lg:w-1/5 xl:w-1/6 lg:z-10 h-full">
					<FilterSortPane
						filters={<AttendanceReportFilters eventTypes={[]} form={sortFilterForm} />}
						closeFn={() => setShowFilters(false)}
					/>
				</div>
			)}
			<div className="grow lg:overflow-x-auto">
				<div className="py-4 px-8">
					<div className="flex gap-2 justify-between">
						<div>
							<p>Last 8 rehearsals: 4 present (50%)</p>
							<p>3-month average: 62%</p>
						</div>

						<Button
							onClick={() => setShowFilters(prev => !prev)}
							variant="secondary"
							size="sm"
						>
							<Icon icon="filter" /> Filter
						</Button>
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
			</div>
		</div>
	);
};

export default AttendanceSectionLarge;
