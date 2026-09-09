import React from 'react';
import { PageHeader2 } from '../../../components/PageHeader/PageHeader';
import Breadcrumbs from '../../../components/PageHeader/Breadcrumbs';
import { PageHeading } from '../../../components/PageHeader/PageHeading';
import PageTopBar, { PageActionsMenu, PageTopBarTitle, PageTopNavigation } from '../../../components/PageTopBar';
import ActionMenuItem from '../../../components/ActionMenu/ActionMenuItem';
import TenantLayout from '../../../Layouts/TenantLayout';
import AppHead from '../../../components/AppHead';
import useRoute from '../../../hooks/useRoute';
import useBulkEdit from '../../../hooks/useBulkEdit';
import BulkEditBar from '../../../components/BulkEditBar';
import Button from '../../../components/inputs/Button';
import Icon from '../../../components/Icon';
import LearningStatusTable from './LearningStatusTable';
import LearningStatusTableMobile from './LearningStatusTableMobile';
import IndexContainer from '../../../components/IndexContainer';
import { router } from '@inertiajs/react';

const Index = ({ song, voiceParts, setSidebarOpen }) => {
	const { route } = useRoute();
	const singers = voiceParts.flatMap(voicePart => voicePart.members.map(singer => ({ ...singer, voicePart })));
	const bulkEdit = useBulkEdit(singers, true, false, 'Singer', true);
	const actions = [bulkEdit.action].filter(Boolean);

	const markSelectedAsPerformanceReady = () => {
		router.post(
			route('songs.singers.bulk-update', { song }),
			{ singer_ids: bulkEdit.selectedIds },
			{ preserveScroll: true, onSuccess: () => bulkEdit.clearSelections() }
		);
	};

	return (
		<>
			<AppHead title={`Learning Status List - ${song.title}`} />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<div className="flex justify-between grow">
					<PageTopNavigation
						backUrl={route('songs.show', { song })}
						breadcrumbs={[
							{ name: 'Songs', url: route('songs.index') },
							{ name: song.title, url: route('songs.show', { song }) },
						]}
					>
						<PageTopBarTitle title="Learning Status List" />
					</PageTopNavigation>
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
				</div>
			</PageTopBar>
			<PageHeader2>
				<div className="lg:flex lg:items-center lg:justify-between">
					<div className="flex-1 min-w-0">
						<div className="hidden lg:block">
							<Breadcrumbs
								breadcrumbs={[
									{ name: 'Songs', url: route('songs.index') },
									{ name: song.title, url: route('songs.show', { song }) },
									{ name: 'Learning Status List', url: route('songs.singers.index', { song }) },
								]}
								showLastChevron={false}
							/>
						</div>
						<PageHeading>
							<Icon icon="list-music" type="solid" className="mr-2" />
							Learning Status List
						</PageHeading>
					</div>
					<div className="hidden lg:flex mt-0 lg:ml-4 gap-3">
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
					</div>
				</div>
			</PageHeader2>

			<BulkEditBar
				bulkEdit={bulkEdit}
				actions={
					<Button size="xs" variant="clear-inverse" onClick={markSelectedAsPerformanceReady}>
						<Icon icon="check-double" className="text-emerald-500" />
						Mark as Performance Ready
					</Button>
				}
			/>

			<IndexContainer
				tableDesktop={<LearningStatusTable song={song} singers={singers} bulkEdit={bulkEdit} />}
				tableMobile={<LearningStatusTableMobile song={song} singers={singers} bulkEdit={bulkEdit} />}
			/>
		</>
	);
};

Index.layout = page => <TenantLayout children={page} />;

export default Index;
