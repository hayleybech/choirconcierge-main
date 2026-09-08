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
import Button from "../../../components/inputs/Button";
import DateTag from "../../../components/DateTag";
import LearningStatus from "../../../LearningStatus";
import LearningStatusTag from "../../../components/Song/LearningStatusTag";
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
									<div className="flex flex-col items-start gap-2">
										<LearningStatusTag status={status} />
										<div className="flex flex-wrap gap-2">
											{status.slug !== 'performance-ready' && (
												<Button href={route('songs.singers.update', { song, singer })} method="put" data={{ status: 'performance-ready' }} size="xs">
													Mark as Performance Ready
												</Button>
											)}
											{status.slug !== 'not-started' && (
												<Button href={route('songs.singers.update', { song, singer })} method="put" data={{ status: 'not-started' }} size="xs">
													Mark as Learning
												</Button>
											)}
										</div>
									</div>
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
