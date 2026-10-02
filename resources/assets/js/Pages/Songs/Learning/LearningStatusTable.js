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
import Badge from "../../../components/Badge";
import SingerStatus from "../../../SingerStatus";
import SingerStatusTag from "../../../components/SingerStatusTag";
import Pagination from "../../../components/Pagination";
import TableHeadingSort from "../../../components/TableHeadingSort";
import { handleNameSort } from "../../../utils/sortHelpers";
import useRoute from "../../../hooks/useRoute";

const LearningStatusTable = ({ song, singers, pagination, bulkEdit, showEnsemble, sortFilterForm }) => {
	const { route } = useRoute();
	return (
		<Table pagination={<Pagination details={pagination} />}>
			<THead>
				<tr>
					<TableSelectAll bulkEdit={bulkEdit} totalItems={singers.length} />
					<TableHeading>
						<TableHeadingSort
							form={sortFilterForm}
							sort={['full-name', 'last-name-first']}
							onClick={() => handleNameSort(sortFilterForm)}
							indicator={sortFilterForm.data.sort === 'full-name' ? 'First' : 'Last'}
						>
							Name
						</TableHeadingSort>
					</TableHeading>
					<TableHeading>Voice Part</TableHeading>
					<TableHeading>
						<TableHeadingSort form={sortFilterForm} sort="learning-status">
							Status
						</TableHeadingSort>
					</TableHeading>
					<TableHeading>
						<TableHeadingSort form={sortFilterForm} sort="learning-updated">
							Updated
						</TableHeadingSort>
					</TableHeading>
				</tr>
			</THead>
			<TBody>
				{singers.map(singer => {
					const status = new LearningStatus(singer.learning.status);

					return (
						<TItemRow key={singer.id} bulkEdit={bulkEdit} value={singer.id}>
							<TableCellSelect bulkEdit={bulkEdit} value={singer.id} />
							<TableCell>
								<div className="flex items-center space-x-3">
									<img className="h-8 w-8 rounded-md object-cover" src={singer.user.avatar_url} alt="" />
									<div>
										<SingerStatusTag status={new SingerStatus(singer.status.status)} />
										<Link
											href={route('singers.show', { singer })}
											className="ml-1 text-sm font-medium text-purple-800"
										>
											{singer.user.name}
										</Link>
									</div>
								</div>
							</TableCell>
							<TableCell>
								<ul className="flex flex-col gap-1.5">
									{singer.enrolments.map(enrolment => (
										<li key={enrolment.id} className="flex gap-1 items-center">
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
										</li>
									))}
								</ul>
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
	);
};

export default LearningStatusTable;
