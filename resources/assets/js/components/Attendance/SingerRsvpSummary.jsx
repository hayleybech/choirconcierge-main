import Badge from '../Badge';

export const SingerRsvpSummary = ({ attendanceSummary }) => {
	const summary = attendanceSummary?.rsvps_next_8;

	return (
		<div className="py-4 px-8">
			{summary ? (
				<div className="flex flex-col gap-4">
					<div>
						<p className="text-sm text-gray-500 mb-1">Upcoming Performance RSVPs (Next 8)</p>
						<div className="flex items-center gap-4">
							<div className="text-3xl font-bold text-gray-900">{summary.percentage}%</div>
							<div className="flex flex-col items-start">
								<span className="text-sm font-medium text-gray-700">
									{summary.responded} / {summary.total} responded
								</span>
								<Badge
									colour={
										summary.percentage >= 80
											? 'bg-emerald-100 text-emerald-800'
											: summary.percentage >= 50
											? 'bg-amber-100 text-amber-800'
											: 'bg-red-100 text-red-800'
									}
								>
									{summary.percentage >= 80
										? 'Excellent'
										: summary.percentage >= 50
										? 'Good'
										: 'Need to respond'}
								</Badge>
							</div>
						</div>
					</div>

					<div className="w-full bg-gray-200 rounded-full h-2.5">
						<div
							className={`h-2.5 rounded-full ${
								summary.percentage >= 80
									? 'bg-emerald-600'
									: summary.percentage >= 50
									? 'bg-amber-500'
									: 'bg-red-600'
							}`}
							style={{ width: `${summary.percentage}%` }}
						></div>
					</div>
				</div>
			) : (
				<p className="text-sm text-gray-500 italic">No upcoming performances available for this summary.</p>
			)}
		</div>
	);
};

export default SingerRsvpSummary;
