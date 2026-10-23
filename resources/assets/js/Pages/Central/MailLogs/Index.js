import React from 'react';

import {
	PageHeader,
	PageHeaderActions,
	PageHeaderContent,
	PageHeaderTitle,
} from '../../../components/PageHeader/PageHeader';
import PageTopBar, { PageActionsMenu, PageTopNavigation } from '../../../components/PageTopBar';
import ActionMenuItem from '../../../components/ActionMenu/ActionMenuItem';
import Button from '../../../components/inputs/ButtonV2';
import Icon from '../../../components/Icon';
import AppHead from '../../../components/AppHead';
import IndexContainer from '../../../components/IndexContainer';
import useRoute from '../../../hooks/useRoute';
import CentralLayout from '../../../Layouts/CentralLayout';
import MailLogTableMobile from './MailLogTableMobile';
import MailLogTableDesktop from './MailLogTableDesktop';
import useSortFilterForm from '../../../hooks/useSortFilterForm';
import useFilterPane from '../../../hooks/useFilterPane';
import FilterSortPane from '../../../components/FilterSortPane';
import Sorts from '../../../components/Sorts';
import MailLogFilters from './MailLogFilters';

const Index = ({ logs, tenants, mailTypes, defaultMailTypes, setSidebarOpen }) => {
	const { route } = useRoute();
	const [showFilters, setShowFilters, filterAction, hasNonDefaultFilters] = useFilterPane();
	const sorts = [
		{ id: 'subject', name: 'Subject' },
		{ id: 'to', name: 'To' },
		{ id: 'from', name: 'From' },
		{ id: 'tenant', name: 'Tenant' },
		{ id: 'size', name: 'Size' },
		{ id: 'opens', name: 'Opens' },
		{ id: 'created_at', name: 'Date Created', default: true, defaultDirection: 'desc' },
	];
	const filters = [
		{ name: 'subject', defaultValue: '' },
		{ name: 'to', defaultValue: '' },
		{ name: 'from', defaultValue: '' },
		{ name: 'tenant', defaultValue: '' },
		{ name: 'type', multiple: true, defaultValue: defaultMailTypes },
	];
	const sortFilterForm = useSortFilterForm('central.mail-logs.index', filters, sorts);

	return (
		<>
			<AppHead title="Mail Logs" />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<PageTopNavigation title="Mail Logs">
					{filterAction && (
						<PageActionsMenu>
							<ActionMenuItem {...filterAction}>
								<Icon icon={filterAction.icon} mr />
								{filterAction.label}
							</ActionMenuItem>
						</PageActionsMenu>
					)}
				</PageTopNavigation>
			</PageTopBar>
			<PageHeader>
				<PageHeaderContent>
					<PageHeaderTitle>
						<Icon icon="history" type="solid" className="mr-2" /> Mail Logs
					</PageHeaderTitle>
				</PageHeaderContent>
				<PageHeaderActions>
					{filterAction && (
						<Button
							onClick={filterAction.onClick}
							size="sm"
							intent={filterAction.intent}
							weight={filterAction.weight}
						>
							<Icon icon={filterAction.icon} mr />
							{filterAction.label}
						</Button>
					)}
				</PageHeaderActions>
			</PageHeader>

			<IndexContainer
				showFilters={showFilters}
				filterPane={
					<FilterSortPane
						sorts={<Sorts sorts={sorts} form={sortFilterForm} />}
						filters={<MailLogFilters form={sortFilterForm} tenants={tenants} mailTypes={mailTypes} />}
						closeFn={() => setShowFilters(false)}
					/>
				}
				tableMobile={
					<MailLogTableMobile
						logs={logs}
						hasNonDefaultFilters={hasNonDefaultFilters}
						setShowFilters={setShowFilters}
					/>
				}
				tableDesktop={<MailLogTableDesktop logs={logs} sortFilterForm={sortFilterForm} />}
			/>
		</>
	);
};

Index.layout = page => <CentralLayout children={page} />;

export default Index;
