import React from 'react';

import TableMobile, { TableMobileListItem } from '../../../components/TableMobile';
import Pagination from '../../../components/Pagination';
import AttendanceRecord from '../../../components/Event/AttendanceRecord';
import Badge from '../../../components/Badge';
import VoicePartTag from '../../../components/VoicePartTag';
import useRoute from '../../../hooks/useRoute';
import { Link } from '@inertiajs/react';

const AttendanceTableMobile = ({ singers, pagination, showEnsemble, event }) => {
	const { route } = useRoute();

	return (
		<TableMobile pagination={<Pagination details={pagination} />}>
			{singers.map(singer => (
				<TableMobileListItem key={singer.id}>
					<div className="flex items-center px-4 py-4 sm:px-6 gap-4">
						<div className="shrink-0">
							<img
								className="h-8 w-8 rounded-md object-cover"
								src={singer.user.avatar_url}
								alt={singer.user.name}
							/>
						</div>
						<div className="min-w-0 flex-1">
							<div className="flex flex-wrap items-center justify-between mb-2 gap-x-2">
								<Link
									href={route('singers.show', { singer })}
									className="text-sm font-medium text-purple-600 truncate"
								>
									{singer.user.name}
								</Link>
								<AttendanceRecord
									attendance={singer.attendance}
									singerId={singer.id}
									event={event}
								/>
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
				</TableMobileListItem>
			))}
		</TableMobile>
	);
};

export default AttendanceTableMobile;
