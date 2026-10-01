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
import BulkEditBar from '../../../components/BulkEditBar';
import Button from '../../../components/inputs/Button';
import Icon from '../../../components/Icon';
import LearningStatusTable from './LearningStatusTable';
import LearningStatusTableMobile from './LearningStatusTableMobile';
import IndexContainer from '../../../components/IndexContainer';
import LearningStatus from '../../../LearningStatus';
import { router } from '@inertiajs/react';

const Index = ({ song, voiceParts, setSidebarOpen }) => {
	const { route } = useRoute();
	const singers = voiceParts.flatMap(voicePart => voicePart.members.map(singer => ({ ...singer, voicePart })));
	const bulkEdit = useBulkEdit(singers, true, false, 'Singer', true);
	const actions = [bulkEdit.action].filter(Boolean);

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

			<IndexContainer
				tableDesktop={<LearningStatusTable song={song} singers={singers} bulkEdit={bulkEdit} />}
				tableMobile={<LearningStatusTableMobile song={song} singers={singers} bulkEdit={bulkEdit} />}
			/>
		</>
	);
};

Index.layout = page => <TenantLayout children={page} />;

export default Index;
