import React from 'react';

import { PageHeader, PageHeaderContent, PageHeaderTitle } from '../../../components/PageHeader/PageHeader';
import PageTopBar, { PageTopNavigation } from '../../../components/PageTopBar';
import Icon from '../../../components/Icon';
import AppHead from '../../../components/AppHead';
import IndexContainer from '../../../components/IndexContainer';
import IndexTableControls from '../../../components/IndexTableControls';
import CentralLayout from '../../../Layouts/CentralLayout';
import MailLogTableMobile from './MailLogTableMobile';
import MailLogTableDesktop from './MailLogTableDesktop';
import useSortFilterForm from '../../../hooks/useSortFilterForm';
import useFilterPane from '../../../hooks/useFilterPane';
import FilterDrawer from '../../../components/FilterDrawer';
import MailLogFilters from './MailLogFilters';

const Index = ({ logs, tenants, mailTypes, defaultMailTypes, setSidebarOpen }) => {
	const [showFilters, setShowFilters] = useFilterPane();
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
		{ name: 'subject', label: 'Subject', type: 'text', defaultValue: '' },
		{ name: 'to', label: 'To', type: 'text', defaultValue: '' },
		{ name: 'from', label: 'From', type: 'text', defaultValue: '' },
		{
			name: 'tenant',
			label: 'Tenant',
			defaultValue: '',
			options: tenants.map(({ id, name }) => ({ id, name })),
			renderOption: option => (
				<span className="inline-flex items-center gap-1.5">
					<Icon icon="building" />
					{option.name}
				</span>
			),
		},
		{ name: 'type', label: 'Mail Type', multiple: true, defaultValue: defaultMailTypes, options: mailTypes },
	];
	const sortFilterForm = useSortFilterForm('central.mail-logs.index', filters, sorts);

	return (
		<>
			<AppHead title="Mail Logs" />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<PageTopNavigation title="Mail Logs" />
			</PageTopBar>
			<PageHeader hideOnMobile>
				<PageHeaderContent>
					<PageHeaderTitle>
						<Icon icon="history" type="solid" className="mr-2" /> Mail Logs
					</PageHeaderTitle>
				</PageHeaderContent>
			</PageHeader>

			<IndexContainer
				showFilters={showFilters}
				tableControls={
					<IndexTableControls
						filters={filters}
						sorts={sorts}
						form={sortFilterForm}
						filterLabel="mail logs"
						onToggleFilters={() => setShowFilters(previousValue => !previousValue)}
					/>
				}
				filterPane={
					<FilterDrawer closeFn={() => setShowFilters(false)}>
						<MailLogFilters form={sortFilterForm} tenants={tenants} mailTypes={mailTypes} />
					</FilterDrawer>
				}
				tableMobile={<MailLogTableMobile logs={logs} />}
				tableDesktop={<MailLogTableDesktop logs={logs} sortFilterForm={sortFilterForm} />}
			/>
		</>
	);
};

Index.layout = page => <CentralLayout children={page} />;

export default Index;
