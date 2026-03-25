import React from 'react';
import { Link } from '@inertiajs/react';
import SongStatusTag from '../../components/SongStatusTag';
import PitchButton from '../../components/PitchButton';
import Table, { TableCell, TableHeading, TBody, THead } from '../../components/Table';
import Badge from '../../components/Badge';
import DateTag from '../../components/DateTag';
import SongStatus from '../../SongStatus';
import TableHeadingSort from '../../components/TableHeadingSort';
import useRoute from '../../hooks/useRoute';
import Pagination from '../../components/Pagination';
import { useInstrument } from '../../hooks/useInstrument';
import CheckboxInput from '../../components/inputs/CheckboxInput';

const SongTableDesktop = ({
	songs,
	sortFilterForm,
	userEnsemblesCount,
	selectedSongIds,
	toggleSongSelection,
	toggleAllSongs,
	refSelectAll,
}) => {
	const { route } = useRoute();

	const [instrument] = useInstrument();

	const showEnsemblesColumn = userEnsemblesCount > 1;

	return (
		<Table pagination={<Pagination details={songs} />}>
			<THead>
				<tr>
					<TableHeading className="w-4 pr-0">
						<CheckboxInput
							ref={refSelectAll}
							checked={selectedSongIds.length === songs.data.length && songs.data.length > 0}
							onChange={toggleAllSongs}
						/>
					</TableHeading>
					<TableHeading>
						<TableHeadingSort form={sortFilterForm} sort="title">
							Title
						</TableHeadingSort>
					</TableHeading>
					<TableHeading>
						<TableHeadingSort form={sortFilterForm} sort="status-title">
							Status
						</TableHeadingSort>
					</TableHeading>
					<TableHeading>Category</TableHeading>
					{showEnsemblesColumn && <TableHeading>Ensembles</TableHeading>}
					<TableHeading>
						<TableHeadingSort form={sortFilterForm} sort="created_at">
							Date Created
						</TableHeadingSort>
					</TableHeading>
				</tr>
			</THead>
			<TBody>
				{songs.data.map(song => (
					<tr key={song.id} className={selectedSongIds.includes(song.id) ? 'bg-purple-50' : ''}>
						<TableCell className="w-4 pr-0">
							<CheckboxInput
								checked={selectedSongIds.includes(song.id)}
								onChange={() => toggleSongSelection(song.id)}
							/>
						</TableCell>
						<TableCell>
							<div className="flex items-center">
								<div>
									<PitchButton instrument={instrument} note={song.pitch.split('/')[0]} size="xs" />
								</div>
								<div className="ml-4 max-w-[45ch] overflow-hidden text-ellipsis whitespace-nowrap">
									<Link
										href={route('songs.show', { song })}
										className="text-sm font-medium text-purple-800"
									>
										{song.title}
									</Link>
								</div>
							</div>
						</TableCell>
						<TableCell>
							<SongStatusTag status={new SongStatus(song.status.slug)} withLabel />
						</TableCell>
						<TableCell>
							<div className="space-x-1.5 space-y-1.5">
								{song.categories.map(category => (
									<Badge key={category.id}>{category.title}</Badge>
								))}
							</div>
						</TableCell>
						{showEnsemblesColumn && (
							<TableCell>
								<div className="space-x-1.5 space-y-1.5">
									{song.ensembles.map(ensemble => (
										<Badge key={ensemble.id} colour="bg-purple-100 text-purple-800">
											{ensemble.name}
										</Badge>
									))}
								</div>
							</TableCell>
						)}
						<TableCell>
							<DateTag icon="pencil" date={song.created_at} />
						</TableCell>
					</tr>
				))}
			</TBody>
		</Table>
	);
};

export default SongTableDesktop;