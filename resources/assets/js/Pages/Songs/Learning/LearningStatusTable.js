import React from 'react';
import { Link } from '@inertiajs/react';
import Table, {
	TableCell,
	TableCellSelect,
	TableHeading,
	TableSelectAll,
	TBody,
	THead,
	TItemRow,
} from "../../../components/Table";
import DateTag from "../../../components/DateTag";
import LearningStatus from "../../../LearningStatus";
import LearningStatusDropdown from "../../../components/Song/LearningStatusDropdown";
import VoicePartTag from "../../../components/VoicePartTag";
import LearningStatusSummary from "./LearningStatusSummary";
import useRoute from "../../../hooks/useRoute";

const LearningStatusTable = ({ song, singers, bulkEdit }) => {
	const { route } = useRoute();
	return (
		<div className="overflow-x-auto">
			<LearningStatusSummary singers={singers} />
			<Table>
				<THead>
					<tr>
						<TableSelectAll bulkEdit={bulkEdit} totalItems={singers.length} />
						<TableHeading>Name</TableHeading>
						<TableHeading>Voice Part</TableHeading>
						<TableHeading>Status</TableHeading>
						<TableHeading>Updated</TableHeading>
					</tr>
				</THead>
				<TBody>
					{singers.map(singer => {
						const status = new LearningStatus(singer.learning.status);

						return (
							<TItemRow key={`${singer.id}-${singer.voicePart.id ?? 'none'}`} bulkEdit={bulkEdit} value={singer.id}>
								<TableCellSelect bulkEdit={bulkEdit} value={singer.id} />
								<TableCell>
									<div className="flex items-center space-x-3">
										<img className="h-8 w-8 rounded-md object-cover" src={singer.user.avatar_url} alt="" />
										<Link
											href={route('singers.show', { singer })}
											className="text-sm font-medium text-purple-800"
										>
											{singer.user.name}
										</Link>
									</div>
								</TableCell>
								<TableCell>
									<VoicePartTag title={singer.voicePart.title} colour={singer.voicePart.colour} />
								</TableCell>
								<TableCell>
									<LearningStatusDropdown
										status={status.slug}
										href={route('songs.singers.update', { song, singer })}
										method="put"
										compact
									/>
								</TableCell>
								<TableCell>
									{singer.learning.updated_at && <DateTag icon="pencil" label="Updated" date={singer.learning.updated_at} format="DATETIME_SHORT" className="text-gray-400" />}
								</TableCell>
							</TItemRow>
						);
					})}
				</TBody>
			</Table>
		</div>
	);
};

export default LearningStatusTable;
