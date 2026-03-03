import Badge from '../Badge';

export const SingerAttendanceSummary = ({ attendanceSummary }) => {

	return (
		<div className="py-4 px-8">
			{attendanceSummary ? (
				<div className="flex flex-col gap-4">
					<div>
						<p className="text-sm text-gray-500 mb-1">Recent Rehearsal Attendance (Last 8 weeks)</p>
						<div className="flex items-center gap-4">
							<div className="text-3xl font-bold text-gray-900">{attendanceSummary.percentage}%</div>
							<div className="flex flex-col items-start">
								<span className="text-sm font-medium text-gray-700">
									{attendanceSummary.attended} / {attendanceSummary.total} attended
								</span>
								<Badge
									colour={
										attendanceSummary.percentage >= 80
											? 'bg-emerald-100 text-emerald-800'
											: attendanceSummary.percentage >= 50
											? 'bg-amber-100 text-amber-800'
											: 'bg-red-100 text-red-800'
									}
								>
									{attendanceSummary.percentage >= 80
										? 'Excellent'
										: attendanceSummary.percentage >= 50
										? 'Good'
										: 'Needs Improvement'}
								</Badge>
							</div>
						</div>
					</div>

					<div className="w-full bg-gray-200 rounded-full h-2.5">
						<div
							className={`h-2.5 rounded-full ${
								attendanceSummary.percentage >= 80
									? 'bg-emerald-600'
									: attendanceSummary.percentage >= 50
									? 'bg-amber-500'
									: 'bg-red-600'
							}`}
							style={{ width: `${attendanceSummary.percentage}%` }}
						></div>
					</div>
				</div>
			) : (
				<p className="text-sm text-gray-500 italic">No attendance summary available for this period.</p>
			)}
		</div>
	);
};

export default SingerAttendanceSummary;
