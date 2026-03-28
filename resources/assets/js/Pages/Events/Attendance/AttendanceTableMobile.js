import React, { useState } from 'react';

import TableMobile, {
	TableMobileHeader,
	TableMobileListItem,
	TableMobileSelect,
	TableMobileSelectableLink,
} from '../../../components/TableMobile';
import Pagination from '../../../components/Pagination';
import AttendanceRecord from '../../../components/Event/AttendanceRecord';
import Badge from '../../../components/Badge';
import VoicePartTag from '../../../components/VoicePartTag';
import useRoute from '../../../hooks/useRoute';
import SingerStatus from '../../../SingerStatus';
import SingerCategoryTag from '../../../components/SingerCategoryTag';

const AttendanceTableMobile = ({ singers, pagination, showEnsemble, event, bulkEdit }) => {
	const { route } = useRoute();
	const [editingSingerId, setEditingSingerId] = useState(null);

	return (
		<div>
			<TableMobileHeader bulkEdit={bulkEdit} />
			<TableMobile pagination={<Pagination details={pagination} />}>
				{singers.map(singer => (
					<TableMobileListItem key={singer.id}>
						<TableMobileSelect bulkEdit={bulkEdit} value={singer.id} />
						<div className="relative">
							<TableMobileSelectableLink
								bulkEdit={bulkEdit}
								value={singer.id}
								url={route('singers.show', { singer })}
							>
								<div className="flex items-center gap-4">
									<div className="shrink-0">
										<img
											className="h-8 w-8 rounded-md object-cover"
											src={singer.user.avatar_url}
											alt={singer.user.name}
										/>
									</div>
									<div
										className={`flex flex-col gap-1 ${
											(!!singer.attendance.absent_reason &&
												(singer.attendance.response === 'absent' ||
													singer.attendance.response === 'absent_apology')) ||
											editingSingerId === singer.id
												? 'pb-16'
												: 'pb-10'
										}`}
									>
										<div>
											<SingerCategoryTag status={new SingerStatus(singer.category.slug)} />
											<span className="ml-1 text-sm font-medium text-purple-600 truncate">
												{singer.user.name}
											</span>
										</div>
										<div className="flex flex-wrap gap-1">
											{singer.enrolments.map(enrolment => (
												<div key={enrolment.id} className="flex gap-1 items-center">
													{showEnsemble && (
														<Badge colour="bg-purple-100 text-purple-800">
															{enrolment.ensemble.name}
														</Badge>
													)}
													{enrolment.voice_part && (
														<VoicePartTag
															title={enrolment.voice_part.title}
															colour={enrolment.voice_part.colour}
														/>
													)}
												</div>
											))}
										</div>
									</div>
								</div>
							</TableMobileSelectableLink>
							<div
								className={`pb-4 flex items-center gap-2 absolute bottom-0 transition-[padding] ${
									bulkEdit.isActiveMobile ? 'pl-11' : 'pl-4'
								}`}
							>
								<div className="pl-12">
									<AttendanceRecord
										attendance={singer.attendance}
										singerId={singer.id}
										event={event}
										onToggleEditing={isEditing => setEditingSingerId(isEditing ? singer.id : null)}
									/>
								</div>
							</div>
						</div>
					</TableMobileListItem>
				))}
			</TableMobile>
		</div>
	);
};

export default AttendanceTableMobile;
