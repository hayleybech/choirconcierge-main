import React from 'react';
import SongStatusTag from '../../components/SongStatusTag';
import TableMobile, {
	TableMobileListItem,
	TableMobileSelect,
	TableMobileSelectableLink,
} from '../../components/Table/TableMobile';
import SongStatus from '../../SongStatus';
import useRoute from '../../hooks/useRoute';
import Pagination from '../../components/Pagination';
import EnsembleBadges from '../../components/EnsembleBadges';
import PitchButton from '../../components/PitchButton';
import { useInstrument } from '../../hooks/useInstrument';
 
const SongTableMobile = ({ songs, userEnsemblesCount, bulkEdit }) => {
	const { route } = useRoute();

	const [instrument] = useInstrument();

	return (
		<div>
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
											size="sm"
											disabled={bulkEdit.isActiveMobile}
										/>
									</div>
									<div className="min-w-0 flex-1 lg:grid lg:grid-cols-2 lg:gap-4">
										<div className="flex items-center justify-between">
											<div className="flex items-center min-w-0 mx-1.5">
												<SongStatusTag status={new SongStatus(song.status.slug)} />
												<span className="text-sm font-medium text-purple-600 truncate">
													{song.title}
												</span>
											</div>
										</div>
									</div>
								</div>
								{userEnsemblesCount > 1 && (
									<EnsembleBadges
										ensembles={song.ensembles}
										className="mt-2 flex gap-1 flex-wrap"
										badgeClassName="truncate"
									/>
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
