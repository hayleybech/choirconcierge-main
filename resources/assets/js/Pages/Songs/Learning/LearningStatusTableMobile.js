import React from 'react';
import TableMobile, {
	TableMobileHeader,
	TableMobileListItem,
	TableMobileSelect,
	TableMobileSelectableLink,
} from "../../../components/TableMobile";
import DateTag from "../../../components/DateTag";
import LearningStatus from "../../../LearningStatus";
import LearningStatusSummary from "./LearningStatusSummary";
import LearningStatusDropdown from "../../../components/Song/LearningStatusDropdown";
import VoicePartTag from "../../../components/VoicePartTag";
import useRoute from "../../../hooks/useRoute";

const LearningStatusTableMobile = ({ song, singers, bulkEdit }) => {
	const { route } = useRoute();

	return (
		<div>
			<LearningStatusSummary singers={singers} />
			<TableMobileHeader bulkEdit={bulkEdit} />
			<TableMobile>
				{singers.map(singer => {
					const status = new LearningStatus(singer.learning.status);

					return (
						<TableMobileListItem key={`${singer.id}-${singer.voicePart.id ?? 'none'}`} className="bg-white">
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
											<span className="text-sm font-medium text-purple-600 truncate">
												{singer.user.name}
											</span>
										</div>
										<VoicePartTag title={singer.voicePart.title} colour={singer.voicePart.colour} />
									</div>
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
