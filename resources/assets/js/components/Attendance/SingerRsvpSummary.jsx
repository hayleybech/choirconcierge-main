import Badge from '../Badge';
import Icon from '../Icon';
import React from 'react';
import ButtonLink from '../inputs/ButtonLink';
import { usePage } from '@inertiajs/react';

export const SingerRsvpSummary = ({ rsvpSummary, performanceTypeId }) => {
			const { tenant } = usePage().props;

			return (
				<div className="py-4 px-4 lg:px-8">
					{rsvpSummary ? (
						<div className="flex flex-col gap-4">
							<div>
								<div className="flex gap-2 justify-between mb-1 items-start">
									<p className="text-sm text-gray-500">Upcoming Performance RSVPs (Next 8)</p>
									<ButtonLink
										href={
											route('events.index', { tenant }) +
											`?filter[date]=upcoming&filter[type.id][]=${performanceTypeId}`
										}
										variant="secondary"
										size="xs"
										className="shrink-0"
									>
										<Icon icon="list" style={{ lineHeight: '1rem' }} />
										<span className="hidden sm:inline">View All</span>
									</ButtonLink>
								</div>
								<div className="flex items-center gap-4">
									<div className="text-3xl font-bold text-gray-900">{rsvpSummary.percentage}%</div>
									<div className="flex flex-col items-start">
										<span className="text-sm font-medium text-gray-700">
											{rsvpSummary.responded} / {rsvpSummary.total} responded
										</span>
										<Badge
											colour={
												rsvpSummary.percentage >= 80
													? 'bg-emerald-100 text-emerald-800'
													: rsvpSummary.percentage >= 50
													? 'bg-amber-100 text-amber-800'
													: 'bg-red-100 text-red-800'
											}
										>
											{rsvpSummary.percentage >= 80
												? 'Excellent'
												: rsvpSummary.percentage >= 50
												? 'Good'
												: 'Need to respond'}
										</Badge>
									</div>
								</div>
							</div>

							<div className="w-full bg-gray-200 rounded-full h-2.5">
								<div
									className={`h-2.5 rounded-full ${
										rsvpSummary.percentage >= 80
											? 'bg-emerald-600'
											: rsvpSummary.percentage >= 50
											? 'bg-amber-500'
											: 'bg-red-600'
									}`}
									style={{ width: `${rsvpSummary.percentage}%` }}
								></div>
							</div>
						</div>
					) : (
						<p className="text-sm text-gray-500 italic">
							No upcoming performances available for this summary.
						</p>
					)}
				</div>
			);
		};

export default SingerRsvpSummary;
