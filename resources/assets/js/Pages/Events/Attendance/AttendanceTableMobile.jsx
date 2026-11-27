import React from 'react';

import TableMobile, {
	TableMobileListItem,
	TableMobileSelect,
} from '../../../components/Table/TableMobile';
import Pagination from '../../../components/Pagination';
import AttendanceRecord from '../../../components/Event/AttendanceRecord';
import Badge from '../../../components/Badge';
import VoicePartTag from '../../../components/VoicePartTag';
import useRoute from '../../../hooks/useRoute';
import { Link } from '@inertiajs/react';

const AttendanceTableMobile = ({ singers, pagination, showEnsemble, event, bulkEdit }) => {
	const { route } = useRoute();

	return (
		<div>
			<TableMobile pagination={<Pagination details={pagination} />}>
				{singers.map(singer => (
					<TableMobileListItem key={singer.id}>
						<TableMobileSelect bulkEdit={bulkEdit} value={singer.id} />
						<div className="flex items-center justify-between">
							<div
								className={`flex items-center pr-4 py-4 sm:pr-6 gap-2 transition-[padding] ease-out ${
									bulkEdit.isActiveMobile ? 'pl-11' : 'pl-4 sm:pl-6'
								}`}
							>
								<div className="shrink-0">
									<img
										className="h-8 w-8 rounded-md object-cover"
										src={singer.user.avatar_url}
										alt={singer.user.name}
									/>
								</div>
								<div className="flex min-w-0 flex-col gap-1">
									<Link
										href={route('singers.show', { singer })}
										className="ml-1 text-sm font-medium text-purple-600 truncate"
									>
										{singer.user.name}
									</Link>
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
							<div className="shrink-0 pr-4">
								<AttendanceRecord
									attendance={singer.attendance}
									singerId={singer.id}
									event={event}
									isMobile
								/>
							</div>
						</div>
					</TableMobileListItem>
				))}
			</TableMobile>
		</div>
	);
};

export default AttendanceTableMobile;
