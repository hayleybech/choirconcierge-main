import React, { Fragment } from 'react';
import { Menu, Transition } from '@headlessui/react';
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
import FilterDrawer from '../../../components/FilterDrawer';
import LearningStatusFilters from '../../../components/LearningStatusFilters';
import LearningStatus from '../../../LearningStatus';
import Badge from '../../../components/Badge';
import VoicePartTag from '../../../components/VoicePartTag';
import SingerStatus from '../../../SingerStatus';
import SingerStatusTag from '../../../components/SingerStatusTag';
import { router } from '@inertiajs/react';
import menuItemStyles from '../../../components/ActionMenu/menuItemStyles';

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
					<span className="inline-flex items-center gap-1.5 text-gray-500">
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
	const moreBulkEditStatuses = ['not-started', 'assessment-ready'];
	const bulkActions = (
		<>
			{Object.keys(LearningStatus.statuses)
				.filter(slug => !moreBulkEditStatuses.includes(slug))
				.map(slug => {
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
			<Menu as="div" className="relative flex">
				<Menu.Button
					as={Button}
					theme="dark"
					weight="clear"
					size="sm"
					className="flex items-center !gap-x-0 font-medium hover:bg-gray-600 focus:outline-none transition ease-in-out duration-150 cursor-pointer"
				>
					More
					<Icon icon="chevron-up" className="ml-1 text-[10px]" />
				</Menu.Button>
				<Transition
					as={Fragment}
					enter="transition ease-out duration-100"
					enterFrom="transform opacity-0 scale-95"
					enterTo="transform opacity-100 scale-100"
					leave="transition ease-in duration-75"
					leaveFrom="transform opacity-100 scale-100"
					leaveTo="transform opacity-0 scale-95"
				>
					<Menu.Items className="origin-bottom-right absolute right-0 bottom-full mb-4 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black/5 focus:outline-none">
						{Object.keys(LearningStatus.statuses)
							.filter(slug => moreBulkEditStatuses.includes(slug))
							.map(slug => {
								const status = new LearningStatus(slug);

								return (
									<Menu.Item key={slug}>
										{({ active }) => (
											<button
												type="button"
												onClick={() => markSelectedAs(slug)}
												className={menuItemStyles('secondary', active, '', 'xs')}
											>
												<Icon icon={status.icon} mr className={status.textColour} />
												{status.title}
											</button>
										)}
									</Menu.Item>
								);
							})}
					</Menu.Items>
				</Transition>
			</Menu>
		</>
	);

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
				actions={bulkActions}
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
					<FilterDrawer closeFn={() => setShowFilters(false)}>
						<LearningStatusFilters
							song={song}
							voiceParts={voiceParts}
							ensembles={ensembles}
							form={sortFilterForm}
							singerStatuses={singerStatuses}
						/>
					</FilterDrawer>
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
