import React from 'react';

import TableMobile, { TableMobileListItem } from '../../../components/TableMobile';
import Pagination from '../../../components/Pagination';
import RsvpTag from "../../../components/Event/RsvpTag";
import Badge from '../../../components/Badge';
import VoicePartTag from '../../../components/VoicePartTag';
import useRoute from "../../../hooks/useRoute";

const RsvpTableMobile = ({ singers, pagination, showEnsemble }) => {
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
									<RsvpTag
										icon={singer.rsvp.icon}
										label={singer.rsvp.label}
										colour={singer.rsvp.colour}
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
							{(singer.user.dietary_requirements || singer.user.medical_conditions) && (
								<div className="mt-2 text-[11px] text-amber-800 bg-amber-50 py-1.5 px-2 rounded space-y-0.5">
									{singer.user.dietary_requirements && (
										<div className="truncate"><span className="font-bold uppercase text-[9px]">Dietary:</span> {singer.user.dietary_requirements}</div>
									)}
									{singer.user.medical_conditions && (
										<div className="truncate"><span className="font-bold uppercase text-[9px]">Medical:</span> {singer.user.medical_conditions}</div>
									)}
								</div>
							)}
						</div>
					</div>
                </TableMobileListItem>
            ))}
        </TableMobile>
    );
}

export default RsvpTableMobile;
