import React from 'react';
import {
	PageHeader,
	PageHeaderActions,
	PageHeaderBreadcrumbs,
	PageHeaderContent,
	PageHeaderTitle,
} from '../../../components/PageHeader/PageHeader';
import PageTopBar, { PageActionsMenu, PageTopNavigation } from '../../../components/PageTopBar';
import ActionMenuItem from '../../../components/ActionMenu/ActionMenuItem';
import TenantLayout from '../../../Layouts/TenantLayout';
import AppHead from '../../../components/AppHead';
import useRoute from '../../../hooks/useRoute';
import useBulkEdit from '../../../hooks/useBulkEdit';
import useFilterPane from '../../../hooks/useFilterPane';
import useSortFilterForm from '../../../hooks/useSortFilterForm';
import BulkEditBar from '../../../components/BulkEditBar';
import Button from '../../../components/inputs/Button';
import Icon from '../../../components/Icon';
import LearningStatusTable from './LearningStatusTable';
import LearningStatusTableMobile from './LearningStatusTableMobile';
import LearningStatusSummary from './LearningStatusSummary';
import IndexContainer from '../../../components/IndexContainer';
import FilterSortPane from '../../../components/FilterSortPane';
import Sorts from '../../../components/Sorts';
import LearningStatusFilters from '../../../components/LearningStatusFilters';
import LearningStatus from '../../../LearningStatus';
import { router } from '@inertiajs/react';

const Index = ({
	song,
	allSingers,
	pagination,
	voiceParts,
	ensembles,
	totalEnsemblesCount,
	singerStatuses,
	counts,
	setSidebarOpen,
}) => {
	const { route } = useRoute();

	const [showFilters, setShowFilters, filterAction] = useFilterPane();

	const showEnsemble = song.ensembles.length > 0 || totalEnsemblesCount > 1;

	const sorts = [
		{ id: 'full-name', name: 'First Name', default: true },
		{ id: 'last-name-first', name: 'Last Name' },
		{ id: 'learning-status', name: 'Learning Status' },
		{ id: 'learning-updated', name: 'Updated' },
	];

	const filters = [
		{ name: 'user.name', defaultValue: '' },
		{ name: 'enrolments.voice_part_id', multiple: true },
		{ name: 'enrolments.ensemble_id', multiple: true },
		{ name: 'learning.status', multiple: true },
		{
			name: 'status.id',
			multiple: true,
			defaultValue: singerStatuses.find(c => c.name === 'Members')?.id
				? [singerStatuses.find(c => c.name === 'Members').id]
				: [],
		},
	];

	const sortFilterForm = useSortFilterForm(['songs.singers.index', { song: song.id }], filters, sorts);

	const bulkEdit = useBulkEdit(allSingers, true, false, 'Singer', true);
	const actions = [filterAction, bulkEdit.action].filter(Boolean);

	const markSelectedAs = (status) => {
		router.post(
			route('songs.singers.bulk-update', { song }),
			{ singer_ids: bulkEdit.selectedIds, status },
			{ preserveScroll: true, onSuccess: () => bulkEdit.clearSelections() }
		);
	};

	const breadcrumbs = [
		{ name: 'Songs', url: route('songs.index') },
		{ name: song.title, url: route('songs.show', { song }) },
		{ name: 'Learning Status List', url: route('songs.singers.index', { song }) },
	];

	return (
		<>
			<AppHead title={`Learning Status List - ${song.title}`} />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
					<PageTopNavigation breadcrumbs={breadcrumbs}></PageTopNavigation>
					{actions.length > 0 && (
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
					)}
			</PageTopBar>
			<PageHeader>
				<PageHeaderContent>
					<PageHeaderBreadcrumbs breadcrumbs={breadcrumbs} />
					<PageHeaderTitle>
						<Icon icon="list-music" type="solid" className="mr-2" />
						Learning Status List
					</PageHeaderTitle>
				</PageHeaderContent>
				<PageHeaderActions>
					{actions.map(action => (
						<Button
							key={action.label}
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

			<BulkEditBar
				bulkEdit={bulkEdit}
				actions={Object.keys(LearningStatus.statuses).map(slug => {
					const status = new LearningStatus(slug);

					return (
						<Button key={slug} size="xs" variant="clear-inverse" onClick={() => markSelectedAs(slug)}>
							<Icon icon={status.icon} className={status.textColour} />
							{status.title}
						</Button>
					);
				})}
			/>

			<LearningStatusSummary counts={counts} />

			<IndexContainer
				showFilters={showFilters}
				filterPane={
					<FilterSortPane
						sorts={<Sorts sorts={sorts} form={sortFilterForm} />}
						filters={
							<LearningStatusFilters
								song={song}
								voiceParts={voiceParts}
								ensembles={ensembles}
								form={sortFilterForm}
								singerStatuses={singerStatuses}
							/>
						}
						closeFn={() => setShowFilters(false)}
					/>
				}
				tableDesktop={
					<LearningStatusTable
						song={song}
						singers={allSingers}
						pagination={pagination}
						bulkEdit={bulkEdit}
						showEnsemble={showEnsemble}
						sortFilterForm={sortFilterForm}
					/>
				}
				tableMobile={
					<LearningStatusTableMobile
						song={song}
						singers={allSingers}
						pagination={pagination}
						bulkEdit={bulkEdit}
						showEnsemble={showEnsemble}
					/>
				}
			/>
		</>
	);
};

Index.layout = page => <TenantLayout children={page} />;

export default Index;
