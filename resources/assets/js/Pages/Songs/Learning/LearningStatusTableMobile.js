import React from 'react';
import TableMobile, {
	TableMobileHeader,
	TableMobileListItem,
	TableMobileSelect,
	TableMobileSelectableLink,
} from "../../../components/TableMobile";
import DateTag from "../../../components/DateTag";
import LearningStatus from "../../../LearningStatus";
import LearningStatusDropdown from "../../../components/Song/LearningStatusDropdown";
import VoicePartTag from "../../../components/VoicePartTag";
import Badge from "../../../components/Badge";
import SingerStatus from "../../../SingerStatus";
import SingerStatusTag from "../../../components/SingerStatusTag";
import Pagination from "../../../components/Pagination";
import useRoute from "../../../hooks/useRoute";

const LearningStatusTableMobile = ({ song, singers, pagination, bulkEdit, showEnsemble }) => {
	const { route } = useRoute();

	return (
		<div>
			<TableMobileHeader bulkEdit={bulkEdit} />
			<TableMobile pagination={<Pagination details={pagination} />}>
				{singers.map(singer => {
					const status = new LearningStatus(singer.learning.status);

					return (
						<TableMobileListItem key={singer.id} className="bg-white">
							<TableMobileSelect bulkEdit={bulkEdit} value={singer.id} />
							<TableMobileSelectableLink
								bulkEdit={bulkEdit}
								value={singer.id}
								url={route('singers.show', { singer })}
							>
								<div className="flex flex-col gap-3 w-full">
									<div className="flex items-center justify-between gap-3">
										<div className="flex items-center gap-3 min-w-0">
											<img
												className="h-8 w-8 rounded-md object-cover shrink-0"
												src={singer.user.avatar_url}
												alt=""
											/>
											<div className="min-w-0">
												<SingerStatusTag status={new SingerStatus(singer.status.status)} />
												<span className="ml-1 text-sm font-medium text-purple-600 truncate">
													{singer.user.name}
												</span>
											</div>
										</div>
									</div>
									<ul className="flex flex-wrap gap-1.5">
										{singer.enrolments.map(enrolment => (
											<li key={enrolment.id} className="flex gap-1 items-center">
												{showEnsemble && (
													<Badge colour="bg-purple-100 text-purple-800">
														{enrolment.ensemble.name}
													</Badge>
												)}
												{enrolment.voice_part && (
													<VoicePartTag title={enrolment.voice_part.title} colour={enrolment.voice_part.colour} />
												)}
											</li>
										))}
									</ul>
									<div className="flex flex-wrap items-center justify-between gap-2 text-sm">
										<LearningStatusDropdown
											status={status.slug}
											href={route('songs.singers.update', { song, singer })}
											method="put"
											compact
										/>
										{singer.learning.updated_at && (
											<DateTag
												icon="pencil"
												date={singer.learning.updated_at}
												format="DATETIME_SHORT"
												className="text-gray-400"
											/>
										)}
									</div>
								</div>
							</TableMobileSelectableLink>
						</TableMobileListItem>
					);
				})}
			</TableMobile>
		</div>
	);
};

export default LearningStatusTableMobile;
