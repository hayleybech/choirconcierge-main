import React from 'react';
import { DateTime } from 'luxon';
import { Bar, BarChart, CartesianGrid, Legend, ResponsiveContainer, Tooltip, XAxis, YAxis } from 'recharts';
import Icon from '../Icon';

const attendanceColours = {
	present: '#10b981',
	late: '#f59e0b',
	absent: '#ef4444',
	unknown: '#6b7280',
};

const attendanceDetails = {
	present: { label: 'On Time', icon: 'check', colour: 'emerald' },
	late: { label: 'Late', icon: 'alarm-snooze', colour: 'amber' },
	absent: { label: 'Absent', icon: 'times', colour: 'red' },
	unknown: { label: 'Not recorded', icon: 'question', colour: 'gray' },
};

const AttendanceLegend = ({ payload = [] }) => (
	<div className="flex flex-wrap justify-center gap-x-4 gap-y-1 pt-2 text-xs font-bold text-gray-700">
		{payload.map(entry => {
			const detail = attendanceDetails[entry.dataKey];

			return (
				<span key={entry.dataKey} className={`text-${detail.colour}-500`}>
					<Icon icon={detail.icon} size="text-xs" mr />
					<span className="text-gray-700">{detail.label}</span>
				</span>
			);
		})}
	</div>
);

const AttendanceTooltip = ({ active, payload, label }) => {
	if (!active || !payload?.length) {
		return null;
	}

	const event = payload[0].payload;

	return (
		<div className="rounded border border-gray-300 bg-white px-3 py-2 text-xs shadow-lg">
			<div className="mb-1 text-[10px] font-medium uppercase tracking-wide text-gray-500">
				{DateTime.fromISO(label).toFormat('dd MMM yyyy')}
			</div>
			<div className="mb-2 max-w-xs font-semibold text-gray-800">{event.title}</div>
			<div className="flex flex-col gap-1">
				{payload.map(entry => {
					const detail = attendanceDetails[entry.dataKey];

					return (
						<div key={entry.dataKey} className="flex items-center justify-between gap-4 font-bold">
							<span className={`text-${detail.colour}-500`}>
								<Icon icon={detail.icon} size="text-xs" mr />
								<span className="text-gray-700">{detail.label}</span>
							</span>
							<span className="text-gray-800">{entry.value}</span>
						</div>
					);
				})}
			</div>
		</div>
	);
};

const AttendanceChart = ({ events, isCollapsed }) => {
	const chartData = events.map(event => ({
		...event,
		...event.attendanceSummary,
	}));

	if (isCollapsed) {
		return null;
	}

	return (
		<div className="bg-white border border-gray-300">
			<div className="h-56 w-full px-4 py-2">
				<ResponsiveContainer width="100%" height="100%">
						<BarChart data={chartData} margin={{ top: 8, right: 16, left: 0, bottom: 8 }}>
							<CartesianGrid strokeDasharray="3 3" />
							<XAxis
								dataKey="start_date"
								tickFormatter={date => DateTime.fromISO(date).toFormat('MM-dd')}
								tick={{ fontSize: '12px' }}

							/>
							<YAxis allowDecimals={false} tick={{ fontSize: '12px' }} />
							<Tooltip content={<AttendanceTooltip />} />
							<Legend content={<AttendanceLegend />} />
							<Bar
								dataKey="present"
								name="On Time"
								stackId="attendance"
								fill={attendanceColours.present}
							/>
							<Bar dataKey="late" name="Late" stackId="attendance" fill={attendanceColours.late} />
							<Bar dataKey="absent" name="Absent" stackId="attendance" fill={attendanceColours.absent} />
							<Bar
								dataKey="unknown"
								name="Not recorded"
								stackId="attendance"
								fill={attendanceColours.unknown}
							/>
						</BarChart>
					</ResponsiveContainer>
				</div>
		</div>
	);
};

export default AttendanceChart;
