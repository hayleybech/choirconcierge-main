import CollapsePanel from '../../../components/CollapsePanel';
import DateTag from '../../../components/DateTag';
import SingerStatus from '../../../SingerStatus';
import SingerStatusTag from '../../../components/SingerStatusTag';
import React from 'react';

export const MembershipHistorySection = ({ singer }) => (
			<CollapsePanel>
				<ul className="">
					{singer.statuses.toReversed().map((historyRecord, idx) => (
						<li key={historyRecord.id}>
							<div className="relative pb-6">
								{idx !== singer.statuses.length - 1 ? (
									<div
										aria-hidden="true"
										className="absolute top-4 left-2 -ml-px h-full w-0.5 bg-gray-500"
									/>
								) : null}
								<div className="relative flex h-6 items-center justify-between">
									<div className="flex items-center">
										<div className="rounded-full border bg-gray-100 border-gray-100 size-8 flex items-center">
											<SingerStatusTag status={historyRecord.status} />{' '}
										</div>
										<span className="text-sm text-gray-700">
											{new SingerStatus(historyRecord.status).title}
										</span>
									</div>
									<DateTag date={historyRecord.created_at} className="text-xs text-gray-400" />{' '}
								</div>
							</div>
						</li>
					))}
				</ul>
			</CollapsePanel>
		);
