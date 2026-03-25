import React, { useState } from 'react';
import PitchButton from '../../components/PitchButton';
import SongStatusTag from '../../components/SongStatusTag';
import TableMobile, { TableMobileLink, TableMobileListItem } from '../../components/TableMobile';
import SongStatus from '../../SongStatus';
import useRoute from '../../hooks/useRoute';
import Pagination from '../../components/Pagination';
import { useInstrument } from '../../hooks/useInstrument';
import Badge from '../../components/Badge';
import CheckboxInput from '../../components/inputs/CheckboxInput';
import Button from '../../components/inputs/Button';
import Icon from '../../components/Icon';

const SongTableMobile = ({
	songs,
	userEnsemblesCount,
	selectedSongIds,
	toggleSongSelection,
	clearSelections,
	setShowBulkEditModal,
	toggleAllSongs,
	refSelectAll,
	isSelectionMode,
	setIsSelectionMode,
}) => {
	const { route } = useRoute();

	const [instrument] = useInstrument();

	return (
		<div>
			{isSelectionMode && (
			<div className="px-4 py-2 border-b border-gray-200 flex gap-1 bg-gray-50">

				<div className="items-center flex mr-3">
					<CheckboxInput
						ref={refSelectAll}
						checked={selectedSongIds.length === songs.data.length && songs.data.length > 0}
						onChange={() => toggleAllSongs()}
					/>
				</div>
				<Button
					size="xs"
					variant={isSelectionMode ? 'primary' : 'secondary'}
					onClick={
						isSelectionMode
							? () => {
									clearSelections();
									setIsSelectionMode(false);
							  }
							: () => setIsSelectionMode(true)
					}
					className="gap-1"
				>
					<Icon icon={isSelectionMode ? 'times' : 'check-square'} />
					{isSelectionMode ? 'Cancel' : 'Select Multiple'}
				</Button>
				{selectedSongIds.length > 0 && (
					<Button
						size="xs"
						variant="secondary"
						onClick={() => setShowBulkEditModal(true)}
						disabled={selectedSongIds.length === 0}
						className="gap-1"
					>
						<Icon icon="pencil" />
						Edit {selectedSongIds.length > 0 ? `(${selectedSongIds.length})` : ''}
					</Button>
				)}

			</div>
			)}
			<TableMobile pagination={<Pagination details={songs} />}>
				{songs.data.map(song => (
					<TableMobileListItem key={song.id}>
						<div className="flex items-center">
							{isSelectionMode && (
								<div className="pl-4 flex">
									<CheckboxInput
										checked={selectedSongIds.includes(song.id)}
										onChange={() => toggleSongSelection(song.id)}
									/>
								</div>
							)}
							<div className="flex-1 min-w-0">
								<div className="flex">
									<div className="shrink-0 py-3 pl-4 flex items-center">
										<PitchButton
											instrument={instrument}
											note={song.pitch.split('/')[0]}
											size="xs"
										/>
									</div>
									<TableMobileLink
										url={isSelectionMode ? null : route('songs.show', { song })}
										onClick={isSelectionMode ? () => toggleSongSelection(song.id) : null}
									>
										<div className="min-w-0 flex-1 lg:grid lg:grid-cols-2 lg:gap-4">
											<div className="flex items-center justify-between">
												<div className="flex items-center min-w-0 mr-1.5">
													<SongStatusTag status={new SongStatus(song.status.slug)} />
													<span className="text-sm font-medium text-purple-600 truncate">
														{song.title}
													</span>
												</div>
											</div>
										</div>
									</TableMobileLink>
								</div>
								{userEnsemblesCount > 1 && song.ensembles.length > 0 && (
									<div className="-mt-2 flex gap-1 flex-wrap mb-3 pl-4">
										{song.ensembles.map(ensemble => (
											<Badge
												key={ensemble.id}
												colour="bg-purple-100 text-purple-800 truncate"
												display=""
											>
												{ensemble.name}
											</Badge>
										))}
									</div>
								)}
							</div>
						</div>
					</TableMobileListItem>
				))}
			</TableMobile>
		</div>
	);
};

export default SongTableMobile;
