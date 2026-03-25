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

const SongTableMobile = ({ songs, userEnsemblesCount, bulkEdit }) => {
	const { route } = useRoute();

	const [instrument] = useInstrument();

	return (
		<div>
			{bulkEdit.isSelectionModeMobile && (
				<div className="px-4 py-2 border-b border-gray-200 flex gap-1 bg-gray-50">
					<div className="items-center flex mr-3">
						<CheckboxInput
							ref={bulkEdit.refSelectAll}
							checked={bulkEdit.selectedIds.length === songs.data.length && songs.data.length > 0}
							onChange={() => bulkEdit.toggleAll()}
						/>
					</div>
					<Button
						size="xs"
						variant={bulkEdit.isSelectionModeMobile ? 'primary' : 'secondary'}
						onClick={
							bulkEdit.isSelectionModeMobile
								? () => {
										bulkEdit.clearSelections();
										bulkEdit.setIsSelectionModeMobile(false);
								  }
								: () => bulkEdit.setIsSelectionModeMobile(true)
						}
						className="gap-1"
					>
						<Icon icon={bulkEdit.isSelectionModeMobile ? 'times' : 'check-square'} />
						{bulkEdit.isSelectionModeMobile ? 'Cancel' : 'Select Multiple'}
					</Button>
					{bulkEdit.selectedIds.length > 0 && (
						<Button
							size="xs"
							variant="secondary"
							onClick={() => bulkEdit.showModal(true)}
							disabled={bulkEdit.selectedIds.length === 0}
							className="gap-1"
						>
							<Icon icon="pencil" />
							Edit {bulkEdit.selectedIds.length > 0 ? `(${bulkEdit.selectedIds.length})` : ''}
						</Button>
					)}
				</div>
			)}
			<TableMobile pagination={<Pagination details={songs} />}>
				{songs.data.map(song => (
					<TableMobileListItem key={song.id}>
						<div className="flex items-center">
							{bulkEdit.isSelectionModeMobile && (
								<div className="pl-4 flex">
									<CheckboxInput
										checked={bulkEdit.selectedIds.includes(song.id)}
										onChange={() => bulkEdit.toggleSelection(song.id)}
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
										url={bulkEdit.isSelectionModeMobile ? null : route('songs.show', { song })}
										onClick={
											bulkEdit.isSelectionModeMobile
												? (e) => {
														bulkEdit.toggleSelection(song.id);
														e.preventDefault();
														return false;
												  }
												: null
										}
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
