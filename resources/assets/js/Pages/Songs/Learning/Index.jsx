import React from 'react';
import {
	PageHeader,
	PageHeaderBreadcrumbs,
	PageHeaderContent,
	PageHeaderTitle,
} from '../../../components/PageHeader/PageHeader';
import PageTopBar, { PageTopNavigation } from '../../../components/PageTopBar';
import TenantLayout from '../../../Layouts/TenantLayout';
import AppHead from '../../../components/AppHead';
import useRoute from '../../../hooks/useRoute';
import useBulkEdit from '../../../hooks/useBulkEdit';
import useFilterPane from '../../../hooks/useFilterPane';
import useSortFilterForm from '../../../hooks/useSortFilterForm';
import BulkEditBar from '../../../components/BulkEditBar';
import Button from '../../../components/inputs/ButtonV2';
import Icon from '../../../components/Icon';
import LearningStatusTable from './LearningStatusTable';
import LearningStatusTableMobile from './LearningStatusTableMobile';
import LearningStatusSummary from './LearningStatusSummary';
import IndexContainer from '../../../components/IndexContainer';
import IndexTableControls from '../../../components/IndexTableControls';
import FilterSortPane from '../../../components/FilterSortPane';
import LearningStatusFilters from '../../../components/LearningStatusFilters';
import LearningStatus from '../../../LearningStatus';
import Badge from '../../../components/Badge';
import VoicePartTag from '../../../components/VoicePartTag';
import SingerStatus from '../../../SingerStatus';
import SingerStatusTag from '../../../components/SingerStatusTag';
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

	const [showFilters, setShowFilters] = useFilterPane();

	const showEnsemble = song.ensembles.length > 0 || totalEnsemblesCount > 1;

	const sorts = [
		{ id: 'full-name', name: 'First Name', default: true },
		{ id: 'last-name-first', name: 'Last Name' },
		{ id: 'learning-status', name: 'Learning Status' },
		{ id: 'learning-updated', name: 'Updated' },
	];

	const filters = [
		{ name: 'user.name', label: 'Name', type: 'text', defaultValue: '' },
		{
			name: 'enrolments.voice_part_id',
			label: 'Voice Part',
			multiple: true,
			options: voiceParts.map(part => ({ id: part.id, name: part.title, colour: part.colour })),
			renderOption: (option, compact) => (
				<VoicePartTag title={option.name} colour={option.colour} compact={compact} />
			),
		},
		...(ensembles.length > 1
			? [
					{
						name: 'enrolments.ensemble_id',
						label: 'Ensemble',
						multiple: true,
						options: ensembles.map(ensemble => ({ id: ensemble.id, name: ensemble.name })),
						renderOption: (option, compact) => (
							<Badge colour="bg-purple-100 text-purple-800" compact={compact}>
								{option.name}
							</Badge>
						),
					},
				]
			: []),
		{
			name: 'learning.status',
			label: 'Learning Status',
			multiple: true,
			options: [
				{ id: 'not-started', name: 'Learning' },
				{ id: 'assessment-ready', name: 'Assessment Ready' },
				{ id: 'performance-ready', name: 'Performance Ready' },
			],
			renderOption: option => {
				const status = new LearningStatus(option.id);

				return (
					<span className="inline-flex items-center gap-1.5">
						<Icon icon={status.icon} className={status.textColour} />
						{status.title}
					</span>
				);
			},
		},
		{
			name: 'status.id',
			label: 'Singer Status',
			multiple: true,
			defaultValue: singerStatuses.find(status => status.name === 'Members')?.id
				? [singerStatuses.find(status => status.name === 'Members').id]
				: [],
			options: singerStatuses.map(status => ({ id: status.id, name: status.name })),
			renderOption: (option, compact) => (
				<SingerStatusTag status={new SingerStatus(option.id)} withLabel compact={compact} />
			),
		},
	];

	const sortFilterForm = useSortFilterForm(['songs.singers.index', { song: song.id }], filters, sorts);

	const bulkEdit = useBulkEdit(allSingers, true, false, 'Singer', true);
	const markSelectedAs = status => {
		router.post(
			route('songs.singers.bulk-update', { song }),
			{ singer_ids: bulkEdit.selectedIds, status },
			{ preserveScroll: true, onSuccess: () => bulkEdit.clearSelections() }
		);
	};

	const breadcrumbs = [
		{ name: 'Songs', url: route('songs.index') },
		{ name: song.title, url: route('songs.show', { song }) },
		{ name: 'Learning Roster', url: route('songs.singers.index', { song }) },
	];

	return (
		<>
			<AppHead title={`Learning Roster - ${song.title}`} />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<PageTopNavigation breadcrumbs={breadcrumbs} />
			</PageTopBar>
			<PageHeader hideOnMobile>
				<PageHeaderContent>
					<PageHeaderBreadcrumbs breadcrumbs={breadcrumbs} />
					<PageHeaderTitle>
						<Icon icon="clipboard-list" className="mr-2" />
						Learning Roster
					</PageHeaderTitle>
				</PageHeaderContent>
			</PageHeader>

			<BulkEditBar
				bulkEdit={bulkEdit}
				actions={Object.keys(LearningStatus.statuses).map(slug => {
					const status = new LearningStatus(slug);

					return (
						<Button
							key={slug}
							size="sm"
							intent="secondary"
							weight="clear"
							theme="dark"
							onClick={() => markSelectedAs(slug)}
						>
							<Icon icon={status.icon} className={status.textColour} />
							{status.title}
						</Button>
					);
				})}
			/>

			<IndexContainer
				showFilters={showFilters}
				tableControls={
					<IndexTableControls
						filters={filters}
						sorts={sorts}
						form={sortFilterForm}
						bulkEdit={bulkEdit}
						filterLabel="learning roster"
						onToggleFilters={() => setShowFilters(previousValue => !previousValue)}
					/>
				}
				tableSummary={<LearningStatusSummary counts={counts} />}
				filterPane={
					<FilterSortPane
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
