import React, { useContext, useEffect, useState } from 'react';
import TenantLayout from '../../Layouts/TenantLayout';
import {
	PageHeader,
	PageHeaderActions,
	PageHeaderBreadcrumbs,
	PageHeaderContent,
	PageHeaderMeta,
	PageHeaderTitle,
} from '../../components/PageHeader/PageHeader';
import PageTopBar, { PageActionsMenu, PageTopNavigation } from '../../components/PageTopBar';
import ActionMenuItem from '../../components/ActionMenu/ActionMenuItem';
import SongStatusTag from '../../components/SongStatusTag';
import PitchButton from '../../components/PitchButton';
import SongAttachmentList from '../../components/SongAttachment/SongAttachmentList';
import LearningSummary from '../../components/Song/LearningSummary';
import MyLearningStatus from '../../components/Song/MyLearningStatus';
import SongCategoryTag from '../../components/Song/SongCategoryTag';
import AppHead from '../../components/AppHead';
import DateTag from '../../components/DateTag';
import DeleteDialog from '../../components/DeleteDialog';
import Pdf from '../../components/Song/Pdf';
import { useMediaQuery } from 'react-responsive';
import { PlayerContext } from '../../contexts/player-context';
import SongStatus from '../../SongStatus';
import Icon from '../../components/Icon';
import Prose from '../../components/Prose';
import ButtonLink from '../../components/inputs/ButtonLink';
import Button from '../../components/inputs/Button';
import SimplePanel from '../../components/SimplePanel';
import SectionLayout from '../Singers/SectionLayout';
import EmptyState from '../../components/EmptyState';
import useRoute from '../../hooks/useRoute';
import { useInstrument } from '../../hooks/useInstrument';

const Show = ({ song, attachment_types, status_count, voice_parts_count, setSidebarOpen }) => {
	const { route } = useRoute();

	const player = useContext(PlayerContext);

	const [instrument, setInstrument] = useInstrument();

	const isMobile = useMediaQuery({ query: '(max-width: 1023px)' });
	const isDesktop = useMediaQuery({ query: '(min-width: 1024px)' });
	const isCompactDesktop = useMediaQuery({ query: '(min-width: 1024px) and (max-width: 1279px)' });

	const [deleteDialogIsOpen, setDeleteDialogIsOpen] = useState(false);
	const actions = [
		{ label: 'Edit', icon: 'edit', url: route('songs.edit', { song }), can: song.can.update_song },
		{
			label: 'Delete',
			icon: 'trash',
			onClick: () => setDeleteDialogIsOpen(true),
			variant: 'danger-outline',
			can: song.can.delete_song,
		},
	].filter(action => action.can);
	const [currentPdf, setCurrentPdf] = useState(() => {
		if (isMobile || isCompactDesktop) {
			return null;
		}
		if (!attachment_types['sheet-music']) {
			return null;
		}
		return attachment_types['sheet-music'][0];
	});

	const showPdf = attachment => {
		setCurrentPdf(attachment);

		if (isMobile || isCompactDesktop) {
			player.setShowFullscreen(true);
		}
	};

	const openFullscreen = () => player.setShowFullscreen(true);
	const closeFullscreen = () => player.setShowFullscreen(false);
	const closeFullscreenMobile = () => {
		player.setShowFullscreen(false);

		if (isMobile || isCompactDesktop) {
			setCurrentPdf(null);
		}
	};

	useEffect(() => {
		return () => {
			closeFullscreenMobile();
		};
	}, []);

	const breadcrumbs = [
		{ name: 'Songs', url: route('songs.index') },
		{ name: song.title, url: route('songs.show', { song }) },
	];
	return (
		<>
			<AppHead title={`${song.title} - Songs`} />

			{player.showFullscreen ? (
				<Pdf
					filename={currentPdf?.download_url}
					isFullscreen={player.showFullscreen}
					openFullscreen={openFullscreen}
					closeFullscreen={closeFullscreenMobile}
					pitch={song.pitch.split('/')[0]}
					instrument={instrument}
					setInstrument={setInstrument}
				/>
			) : (
				<>
					<PageTopBar setSidebarOpen={setSidebarOpen}>
						<PageTopNavigation breadcrumbs={breadcrumbs}></PageTopNavigation>
						<PageActionsMenu>
							{actions.map((action, key) => (
								<ActionMenuItem
									key={key}
									url={action.url}
									onClick={action.onClick}
									variant={action.variant}
								>
									<Icon icon={action.icon} mr />
									{action.label}
								</ActionMenuItem>
							))}
						</PageActionsMenu>
					</PageTopBar>
					<PageHeader>
						<PageHeaderContent>
							<PageHeaderBreadcrumbs breadcrumbs={breadcrumbs} />
							<PageHeaderTitle>{song.title}</PageHeaderTitle>
							<PageHeaderMeta>
								<PitchButton instrument={instrument} note={song.pitch.split('/')[0]} size="sm" />
								<SongStatusTag status={new SongStatus(song.status.slug)} withLabel />
								{song.categories.length > 0 && (
									<div className="space-x-1.5 flex items-center">
										{song.categories.map(category => (
											<React.Fragment key={category.id}>
												<SongCategoryTag category={category} />
											</React.Fragment>
										))}
									</div>
								)}
								{song.ensembles.length > 0 && (
									<div className="space-x-1.5 flex items-center">
										{song.ensembles.map(ensemble => (
											<span
												key={ensemble.id}
												className="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800"
											>
												{ensemble.name}
											</span>
										))}
									</div>
								)}
								<div className="flex items-center gap-2">
									<DateTag icon="pencil" date={song.created_at} label="Created" />
									<DateTag icon="pencil" date={song.updated_at} label="Updated" />
								</div>
								{!!song.show_for_prospects && (
									<div>
										<Icon icon="microphone-stand" mr className="text-sm text-emerald-500" />
										<span className="text-sm font-medium text-gray-500 truncate">
											Audition Song
										</span>
									</div>
								)}
							</PageHeaderMeta>
						</PageHeaderContent>
						<PageHeaderActions>
							{actions.map((action, key) => (
								<Button
									key={key}
									href={action.url}
									onClick={action.onClick}
									size="sm"
									variant={action.variant}
								>
									<Icon icon={action.icon} mr />
									{action.label}
								</Button>
							))}
						</PageHeaderActions>
					</PageHeader>
					<DeleteDialog
						title="Delete Song"
						url={route('songs.destroy', { song })}
						isOpen={deleteDialogIsOpen}
						setIsOpen={setDeleteDialogIsOpen}
					>
						Are you sure you want to delete this song? All of its attachments will be permanently removed
						from our servers forever. This action cannot be undone.
					</DeleteDialog>

					<SectionLayout
						gridClassName="grid-cols-1 divide-y divide-gray-300 sm:grid sm:grid-cols-2 sm:divide-x sm:divide-y-0 xl:grid-cols-4 lg:overflow-y-auto"
						columnClassNames={[
							'sm:col-span-1 sm:border-r sm:border-r-gray-300 sm:order-1 flex flex-col justify-stretch',
							'hidden xl:block sm:col-span-2 xl:col-span-2 sm:order-3 xl:order-2 overflow-hidden',
							'sm:col-span-1 sm:order-2 xl:order-3',
						]}
						columns={[
							[
								{
									title: 'Attachments',
									hideTitleOnDesktop: true,
									key: 'attachments',
									show: true,
									content: (
										<>
											<SongAttachmentList
												attachmentTypes={attachment_types}
												song={song}
												currentPdf={currentPdf}
												setCurrentPdf={showPdf}
												player={player}
											/>
										</>
									),
								},
							],
							[
								{
									key: 'Sheet Music',
									show: isDesktop && !isCompactDesktop && !player.showFullscreen,
									showOnMobile: false,
									collapsible: false,
									content: !!currentPdf ? (
										<Pdf
											filename={currentPdf?.download_url}
											isFullscreen={player.showFullscreen}
											openFullscreen={openFullscreen}
											closeFullscreen={closeFullscreen}
											pitch={song.pitch.split('/')[0]}
											instrument={instrument}
											setInstrument={setInstrument}
										/>
									) : (
										<EmptyState
											title="No sheet music"
											description={
												<>
													This prime location is reserved for displaying your sheet music.{' '}
													<br />
													It looks like you don't have any yet for this song.
												</>
											}
											actionDescription={
												song.can['update_song']
													? 'To add some, use the "Add Attachment" form on the left.'
													: null
											}
											icon="file-pdf"
										/>
									),
								},
							],
							[
								{
									title: 'Learning',
									hideTitleOnDesktop: true,
									show: true,
									content: (
										<>
											<div className="py-2 px-4 bg-gray-100 border-b border-gray-200 flex items-center justify-between gap-2">
												<h3 className="font-semibold text-gray-700">My Learning Status</h3>
											</div>

											<MyLearningStatus song={song} />
											<hr className="border-gray-200" />
											{song.can['update_song'] && (
												<>
													<div className="py-2 px-4 bg-gray-100 border-b border-gray-200 flex items-center justify-between gap-2">
														<h3 className="font-semibold text-gray-700">
															Learning Summary
														</h3>
														<EditLearningSummaryButton song={song} />
													</div>

													<LearningSummary
														status_count={status_count}
														voice_parts_count={voice_parts_count}
														song={song}
													/>
												</>
											)}
										</>
									),
								},
								{
									title: 'Description',
									show: true,
									content: <SongDescription description={song.description} />,
								},
							],
						]}
					/>
				</>
			)}
		</>
	);
};

Show.layout = page => <TenantLayout children={page} />;

export default Show;

const SongDescription = ({ description }) => (
	<SimplePanel>
		<Prose content={description ?? 'No description'} className="mb-8" />
	</SimplePanel>
);

const EditLearningSummaryButton = ({ song }) => {
	const { route } = useRoute();

	return (
		<ButtonLink variant="primary" size="xs" href={route('songs.singers.index', { song })}>
			<Icon icon="edit" />
			Edit
		</ButtonLink>
	);
};
