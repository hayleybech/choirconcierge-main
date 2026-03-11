import React from 'react';

import TableMobile, { TableMobileListItem } from '../../../components/TableMobile';
import Pagination from '../../../components/Pagination';
import AttendanceTag from "../../../components/Event/AttendanceTag";
import Badge from '../../../components/Badge';
import VoicePartTag from '../../../components/VoicePartTag';
import useRoute from "../../../hooks/useRoute";

const AttendanceTableMobile = ({ singers, pagination, showEnsemble }) => {
    const { route } = useRoute();

    return (
        <TableMobile pagination={<Pagination details={pagination} />}>
            {singers.map((singer) => (
                <TableMobileListItem key={singer.id} url={route('singers.show', {singer: singer.id})}>
					<div className="flex items-center px-4 py-4 sm:px-6 gap-4">
						<div className="shrink-0">
							<img className="h-10 w-10 rounded-md object-cover" src={singer.user.avatar_url} alt={singer.user.name} />
						</div>
						<div className="min-w-0 flex-1">
							<div className="flex items-center justify-between mb-1">
								 <span className="text-sm font-medium text-purple-600 truncate">{singer.user.name}</span>
								 <div className="scale-90 origin-right">
									<AttendanceTag
										icon={singer.attendance.icon}
										label={singer.attendance.label}
										colour={singer.attendance.colour}
									/>
								 </div>
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
}

export default AttendanceTableMobile;
