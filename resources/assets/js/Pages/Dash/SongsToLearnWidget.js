import React from 'react';
import Panel, { PanelTitle } from '../../components/Panel';
import TableMobile, { TableMobileItem } from '../../components/TableMobile';
import LearningStatusTag from '../../components/Song/LearningStatusTag';
import LearningStatus from '../../LearningStatus';
import useRoute from '../../hooks/useRoute';
import Button from '../../components/inputs/Button';
import Icon from '../../components/Icon';

const SongsToLearnWidget = ({ songs }) => {
	const { route } = useRoute();

	return (
		<Panel
			header={
				<div className="flex justify-between items-center">
					<PanelTitle>Songs to Learn</PanelTitle>
					<Button href={route('songs.index')} variant="secondary" size="xs">
						<Icon icon="list" style={{ lineHeight: '1rem' }} />
						<span className="hidden sm:inline">View All</span>
					</Button>
				</div>
			}
			noPadding
		>
			{songs.length > 0 ? (
				<TableMobile>
					{songs.map(song => (
						<TableMobileItem url={route('songs.show', { song })} key={song.id}>
							<div className="flex justify-between items-center gap-1 grow">
								<div className="text-sm font-medium text-purple-800 shrink-1">{song.title}</div>
								<div className="text-sm">
									<LearningStatusTag status={new LearningStatus(song.my_learning.status)} />
								</div>
							</div>
						</TableMobileItem>
					))}
				</TableMobile>
			) : (
				<p className="px-4 py-4 sm:px-6">No new songs to learn.</p>
			)}
		</Panel>
	);
};

export default SongsToLearnWidget;
