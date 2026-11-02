import React from 'react';
 
import TenantLayout from '../../Layouts/TenantLayout';
import SongTableDesktop from './SongTableDesktop';
import SongTableMobile from './SongTableMobile';
import {
	PageHeader,
	PageHeaderActions,
	PageHeaderContent,
	PageHeaderLink,
	PageHeaderLinks,
	PageHeaderTitle,
	PageHeaderTitleRow,
} from '../../components/PageHeader/PageHeader';
import PageTopBar, { PageActionsMenu, PageTopNavigation } from '../../components/PageTopBar';
import ActionMenuItem from '../../components/ActionMenu/ActionMenuItem';
import Button from '../../components/inputs/ButtonV2';
import Icon from '../../components/Icon';
import AppHead from '../../components/AppHead';
import SongFilters from '../../components/Song/SongFilters';
import IndexContainer from '../../components/IndexContainer';
import IndexTableControls from '../../components/IndexTableControls';
import useFilterPane from '../../hooks/useFilterPane';
import FilterSortPane from '../../components/FilterSortPane';
import useSortFilterForm from '../../hooks/useSortFilterForm';
import EmptyState from '../../components/EmptyState';
import useRoute from '../../hooks/useRoute';
import BulkEditSongsModal from './BulkEditSongsModal';
import useBulkEdit from '../../hooks/useBulkEdit';
import Dialog from '../../components/Dialog';
import BulkEditBar from '../../components/BulkEditBar';

const Index = ({
	songs,
	statuses,
	defaultStatuses,
	categories,
	showForProspectsDefault,
	userEnsemblesCount,
	ensembles,

	can,
	setSidebarOpen,
}) => {
	const [showFilters, setShowFilters] = useFilterPane();
	const { route } = useRoute();

	const bulkEdit = useBulkEdit(songs.data, can.update_song, can.delete_song, 'Song');

	const sorts = [
		{ id: 'title', name: 'Title', default: true },
		{ id: 'created_at', name: 'Date Created' },
		{ id: 'status-title', name: 'Status' },
	];

	const filters = [
		{ name: 'title', label: 'Title', type: 'text', defaultValue: '' },
		{
			name: 'status.id',
			label: 'Status',
			multiple: true,
			defaultValue: defaultStatuses,
			options: statuses.map(status => ({ id: status.id, name: status.title })),
		},
		{
			name: 'categories.id',
			label: 'Category',
			multiple: true,
			options: categories.map(category => ({ id: category.id, name: category.title })),
		},
		...(userEnsemblesCount > 1
			? [
					{
						name: 'ensembles.id',
						label: 'Ensemble',
						multiple: true,
						options: ensembles.map(ensemble => ({ id: ensemble.id, name: ensemble.name })),
					},
				]
			: []),
		...(showForProspectsDefault.length > 1
			? [
					{
						name: 'show_for_prospects',
						label: 'Audition Songs',
						multiple: true,
						multipleBool: true,
						defaultValue: showForProspectsDefault,
						options: [
							{ id: false, name: 'Non-Audition Songs' },
							{ id: true, name: 'Audition Songs' },
						],
					},
				]
			: []),
	];

	const sortFilterForm = useSortFilterForm('songs.index', filters, sorts);

	const actions = [
		{
			label: 'Add New',
			icon: 'plus',
			url: route('songs.create'),
			intent: 'primary',
			weight: 'solid',
			can: 'create_song',
		},
		{ label: 'Categories', icon: 'tags', url: route('song-categories.index'), can: 'list_songs' },
		bulkEdit.action,
	]
		.filter(action => !!action)
		.filter(action => (action.can ? can[action.can] : true));
	const categoryAction = actions.find(action => action.label === 'Categories');
	const pageActions = actions.filter(action => action !== categoryAction);
	const pageMenuActions = actions.filter(action => action !== bulkEdit.action);

	return (
		<>
			<AppHead title="Songs" />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<PageTopNavigation title="Songs">
					<PageActionsMenu>
						{pageMenuActions.map((action, key) => (
							<ActionMenuItem key={key} url={action.url} onClick={action.onClick} intent={action.intent}>
								<Icon icon={action.icon} mr />
								{action.label}
							</ActionMenuItem>
						))}
					</PageActionsMenu>
				</PageTopNavigation>
			</PageTopBar>
			<PageHeader hideOnMobile>
				<PageHeaderContent>
					<PageHeaderTitleRow>
						<PageHeaderTitle>
							<Icon icon="list-music" type="solid" className="mr-2" />
							Songs
						</PageHeaderTitle>
						<PageHeaderLinks>
							{categoryAction && (
								<PageHeaderLink href={categoryAction.url} icon={categoryAction.icon}>
									{categoryAction.label}
								</PageHeaderLink>
							)}
						</PageHeaderLinks>
					</PageHeaderTitleRow>
				</PageHeaderContent>
				<PageHeaderActions>
					{pageActions.map(action => (
						<Button
							key={action.label}
							href={action.url}
							onClick={action.onClick}
							size="md"
							intent={action.intent}
							weight={action.weight}
							className={action.className}
						>
							<Icon icon={action.icon} mr />
							{action.label}
						</Button>
					))}
				</PageHeaderActions>
			</PageHeader>

			<Dialog
				title={`Delete ${bulkEdit.selectedIds.length} Songs?`}
				isOpen={bulkEdit.showDeleteModal}
				setIsOpen={bulkEdit.setShowDeleteModal}
				okLabel="Delete"
				okColour="danger"
				okMethod="post"
				data={{ song_ids: bulkEdit.selectedIds }}
				okUrl={route('songs.bulk-destroy')}
				onOk={() => {
					bulkEdit.setSelectedIds([]);
					bulkEdit.setShowDeleteModal(false);
					bulkEdit.setIsForcedMobile(false);
				}}
			>
				Are you sure you want to delete the selected songs? This action cannot be undone.
			</Dialog>

			<BulkEditBar bulkEdit={bulkEdit} />

			<IndexContainer
				showFilters={showFilters}
				tableControls={
					<IndexTableControls
						filters={filters}
						sorts={sorts}
						form={sortFilterForm}
						bulkEdit={bulkEdit}
						filterLabel="songs"
						onToggleFilters={() => setShowFilters(previousValue => !previousValue)}
					/>
				}
				filterPane={
					<FilterSortPane
						filters={
							<SongFilters
								statuses={statuses}
								categories={categories}
								ensembles={ensembles}
								userEnsemblesCount={userEnsemblesCount}
								showForProspectsDefault={showForProspectsDefault}
								form={sortFilterForm}
							/>
						}
						closeFn={() => setShowFilters(false)}
					/>
				}
				tableMobile={
					<SongTableMobile
						songs={songs}
						userEnsemblesCount={userEnsemblesCount}
						bulkEdit={bulkEdit}
					/>
				}
				tableDesktop={
					<SongTableDesktop
						songs={songs}
						sortFilterForm={sortFilterForm}
						userEnsemblesCount={userEnsemblesCount}
						bulkEdit={bulkEdit}
					/>
				}
				emptyState={
					songs.data.length === 0 ? (
						<EmptyState
							title="No songs"
							description="You don't have any songs yet, or you need to expand your filters. "
							actionDescription={
								can['create_song']
									? 'To get started, add a song then upload some sheet music or audio files. '
									: null
							}
							icon="list-music"
							href={can['create_song'] ? route('songs.create') : null}
							actionLabel="Add Song"
							actionIcon="plus"
						/>
					) : null
				}
			/>

			<BulkEditSongsModal
				isOpen={bulkEdit.showEditModal}
				setIsOpen={bulkEdit.setShowEditModal}
				selectedSongIds={bulkEdit.selectedIds}
				onSuccess={() => bulkEdit.setSelectedIds([])}
				statuses={statuses}
				categories={categories}
				ensembles={ensembles}
				userEnsemblesCount={userEnsemblesCount}
				key={bulkEdit.selectedIds}
			/>
		</>
	);
};

Index.layout = page => <TenantLayout children={page} />;

export default Index;
