import React from 'react';
import PitchButton from '../../components/PitchButton';
import SongStatusTag from '../../components/SongStatusTag';
import TableMobile, {
	TableMobileListItem,
	TableMobileSelect,
	TableMobileSelectableLink,
} from '../../components/TableMobile';
import SongStatus from '../../SongStatus';
import useRoute from '../../hooks/useRoute';
import Pagination from '../../components/Pagination';
import { useInstrument } from '../../hooks/useInstrument';
import Badge from '../../components/Badge';
import BulkEditBarMobile from '../../components/BulkEditBarMobile';

const SongTableMobile = ({ songs, userEnsemblesCount, bulkEdit }) => {
	const { route } = useRoute();

	const [instrument] = useInstrument();

	return (
		<div>
			<BulkEditBarMobile totalItems={songs.data.length} bulkEdit={bulkEdit} />
			<TableMobile pagination={<Pagination details={songs} />}>
				{songs.data.map(song => (
					<TableMobileListItem key={song.id}>
						<TableMobileSelect bulkEdit={bulkEdit} value={song.id} />
						<TableMobileSelectableLink
							url={route('songs.show', { song })}
							bulkEdit={bulkEdit}
							value={song.id}
						>
							<div className="flex-1 min-w-0">
								<div className="flex">
									<div className="shrink-0 flex items-center">
										<PitchButton
											instrument={instrument}
											note={song.pitch.split('/')[0]}
											size="xs"
											disabled={bulkEdit.isSelectionModeMobile}
										/>
									</div>
									<div className="min-w-0 flex-1 lg:grid lg:grid-cols-2 lg:gap-4">
										<div className="flex items-center justify-between">
											<div className="flex items-center min-w-0 mr-1.5 px-4">
												<SongStatusTag status={new SongStatus(song.status.slug)} />
												<span className="text-sm font-medium text-purple-600 truncate">
													{song.title}
												</span>
											</div>
										</div>
									</div>
								</div>
								{userEnsemblesCount > 1 && song.ensembles.length > 0 && (
									<div className="mt-2 flex gap-1 flex-wrap pl-4">
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
						</TableMobileSelectableLink>
					</TableMobileListItem>
				))}
			</TableMobile>
		</div>
	);
};

export default SongTableMobile;
