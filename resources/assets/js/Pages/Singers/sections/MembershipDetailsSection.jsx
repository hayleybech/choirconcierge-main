import SimplePanel from '../../../components/SimplePanel';
import { DetailList, DetailListItem } from '../components/DetailList';
import Badge from '../../../components/Badge';
import FeeStatus from '../../../components/FeeStatus';
import DateTag from '../../../components/DateTag';
import React from 'react';
import SingerStatus from '../../../SingerStatus';
import SingerStatusTag from '../../../components/SingerStatusTag';

export const MembershipDetailsSection = ({ singer, can }) => (
			<SimplePanel>
				<div className="flex flex-col sm:flex-row">
					<DetailList gridCols="grid-cols-2 md:grid-cols-4">
						<DetailListItem label="Membership Status">
							<SingerStatusTag status={new SingerStatus(singer.status.status)} withLabel />
						</DetailListItem>
						<DetailListItem label="Member Since">
							<DateTag date={singer.joined_at} />
						</DetailListItem>
						<DetailListItem label="Roles" colClass="sm:col-span-2">
							<div className="space-x-1.5 space-y-1.5">
								{singer.roles.map(role => (
									<Badge key={role.name}>{role.name.split(' ')[0]}</Badge>
								))}
							</div>
						</DetailListItem>
						<DetailListItem label="Membership Fees">
							<FeeStatus status={singer.fee_status} />
							{singer.paid_until && (
								<span className="text-sm text-gray-500 italic">
									<DateTag date={singer.paid_until} label="Expires" />
								</span>
							)}
						</DetailListItem>
						<DetailListItem label="Referred by">{singer.referrer ?? 'Unknown'}</DetailListItem>
						<DetailListItem label="Reason for Joining">
							{singer.reason_for_joining ?? 'Unknown'}
						</DetailListItem>
						<DetailListItem label="Notes / Membership Details" colClass="col-span-2">
							{singer.membership_details ?? 'N/A'}
						</DetailListItem>
						<DetailListItem label="Last Login">
							<DateTag icon="sign-in" date={singer.user.last_login} />
						</DetailListItem>
						<DetailListItem>
							<span className="text-sm text-gray-500 italic">
								<DateTag icon="pencil" date={singer.created_at} label="Created" />
								<DateTag icon="pencil" date={singer.created_at} label="Updated" />
							</span>
						</DetailListItem>
					</DetailList>
					{can['view_member_history'] && (
						<div className="shrink-0 mt-4 sm:mt-0">
							<div className="text-sm font-medium text-gray-500 mb-2">Membership History</div>
							<ul className="overflow-auto shrink-0 max-h-[300px]">
								{singer.statuses.toReversed().map((statusRecord, idx) => (
									<li key={statusRecord.id}>
										<div className="relative pb-6">
											{idx !== singer.statuses.length - 1 ? (
												<div
													aria-hidden="true"
													className="absolute top-4 left-2 -ml-px h-full w-0.5 bg-gray-500"
												/>
											) : null}
											<div className="relative flex h-6 items-center justify-between gap-4">
												<div className="flex items-center">
													<div className="rounded-full border bg-gray-100 border-gray-100 size-8 flex items-center">
														<SingerStatusTag
															status={new SingerStatus(statusRecord.status)}
														/>{' '}
													</div>
													<span className="text-sm text-gray-700">
														{new SingerStatus(statusRecord.status).title}
													</span>
												</div>
												<DateTag
													date={statusRecord.created_at}
													className="text-xs text-gray-400"
												/>{' '}
											</div>
										</div>
									</li>
								))}
							</ul>
						</div>
					)}
				</div>
			</SimplePanel>
		);
