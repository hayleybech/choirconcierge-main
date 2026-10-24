import React, { useState } from 'react';
import { Link, usePage } from '@inertiajs/react';
import VoicePartTag from '../../components/VoicePartTag';
import SingerStatusTag from '../../components/SingerStatusTag';
import Table, {
	TableCell,
	THead,
	TBody,
	TableHeading,
	TableSelectAll,
	TableCellSelect,
	TItemRow,
} from '../../components/Table';
import Icon from '../../components/Icon';
import SingerStatus from '../../SingerStatus';
import FeeStatus from '../../components/FeeStatus';
import DateTag from '../../components/DateTag';
import TableHeadingSort from '../../components/TableHeadingSort';
import useRoute from '../../hooks/useRoute';
import Pagination from '../../components/Pagination';
import Badge from '../../components/Badge';
import { handleNameSort } from '../../utils/sortHelpers';
import ButtonV2 from '../../components/inputs/ButtonV2';
import RenewFeesDialog from './components/RenewFeesDialog';
import { DateTime } from 'luxon';

const SingerTableDesktop = ({ singers, sortFilterForm, pagination, ensembles, bulkEdit }) => {
	const [feeDialogIsOpen, setFeeDialogIsOpen] = useState(false);
	const [renewingSinger, setRenewingSinger] = useState(singers[0] ?? null);

	const { can, tenant } = usePage().props;
	const { route } = useRoute();

	const showEnsembleColumn = (ensembles ?? tenant.ensembles).length > 1;

	return (
		<>
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
						<TableHeading>{showEnsembleColumn ? 'Ensembles' : 'Voice Part'}</TableHeading>
						<TableHeading>
							<TableHeadingSort form={sortFilterForm} sort="status-title">
								Status
							</TableHeadingSort>
						</TableHeading>
						<TableHeading>Email</TableHeading>
						{can['manage_finances'] && (
							<TableHeading>
								<TableHeadingSort form={sortFilterForm} sort="paid_until">
									Fees
								</TableHeadingSort>
							</TableHeading>
						)}
					</tr>
				</THead>
				<TBody>
					{singers.map(singer => (
						<TItemRow key={singer.id} bulkEdit={bulkEdit} value={singer.id}>
							<TableCellSelect bulkEdit={bulkEdit} value={singer.id} />
							<TableCell>
								<div className="flex items-center">
									<div className="shrink-0 h-10 w-10">
										<img
											className="h-10 w-10 rounded-md"
											src={singer.user.avatar_url}
											alt={singer.user.name}
										/>
									</div>
									<div className="ml-4">
										<Link
											href={route('singers.show', { singer: singer.id })}
											className="text-sm font-medium text-purple-800"
										>
											{singer.user.name}
										</Link>
										<div>
											<Icon icon="phone" mr className="text-gray-400" />
											{singer.user.phone ? (
												<a href={`tel:${singer.user.phone}`} target="_blank">
													{singer.user.phone}
												</a>
											) : (
												'No phone'
											)}
										</div>
									</div>
								</div>
							</TableCell>
							<TableCell>
								<ul className="flex flex-col gap-1.5">
									{singer.enrolments.map(enrolment => (
										<li key={enrolment.id} className="flex gap-1 items-center mb-2">
											{showEnsembleColumn && (
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
								<SingerStatusTag status={new SingerStatus(singer.status.status)} withLabel />
							</TableCell>
							<TableCell>
								<Icon icon="envelope" mr className="text-gray-400" />
								<a href={`mailto:${singer.user.email}`} target="_blank">
									{singer.user.email}
								</a>
							</TableCell>
							{can['manage_finances'] && (
								<TableCell>
									<div className="flex gap-2 items-center">
										<FeeStatus
											status={singer.fee_status}
											tooltip={`Expires ${DateTime.fromJSDate(
												new Date(singer.paid_until)
											).toLocaleString(DateTime['DATE_MED'])}`}
										/>
									</div>
									<ButtonV2
										intent="success"
										weight="outline"
										size="xs"
										onClick={() => {
											setRenewingSinger(singer);
											setFeeDialogIsOpen(true);
										}}
										className="mt-1"
									>
										<Icon icon="calendar-check" />
										Renew
									</ButtonV2>
								</TableCell>
							)}
						</TItemRow>
					))}
				</TBody>
			</Table>
			<RenewFeesDialog isOpen={feeDialogIsOpen} setIsOpen={setFeeDialogIsOpen} singer={renewingSinger} />
		</>
	);
};

export default SingerTableDesktop;
