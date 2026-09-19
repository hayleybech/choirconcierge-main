import React from 'react';
import TenantLayout from '../../Layouts/TenantLayout';
import {
	PageHeader,
	PageHeaderBreadcrumbs,
	PageHeaderContent,
	PageHeaderTitle,
} from '../../components/PageHeader/PageHeader';
import PageTopBar, { PageTopNavigation } from '../../components/PageTopBar';
import Icon from '../../components/Icon';
import AppHead from '../../components/AppHead';
import RiserStackForm from './RiserStackForm';
import useRoute from '../../hooks/useRoute';

const Create = ({ voiceParts, singers, ensembles, setSidebarOpen }) => {
	const { route } = useRoute();

	const breadcrumbs = [
		{ name: 'Riser Stacks', url: route('stacks.index') },
		{ name: 'Create Riser Stack', url: route('stacks.create') },
	];

	return (
		<>
			<AppHead title="Create Riser Stack" />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<PageTopNavigation breadcrumbs={breadcrumbs}></PageTopNavigation>
			</PageTopBar>
			<PageHeader>
				<PageHeaderContent>
					<PageHeaderBreadcrumbs breadcrumbs={breadcrumbs} />
					<PageHeaderTitle>
						<Icon icon="people-arrows" type="solid" className="mr-2" />
						Create Riser Stack
					</PageHeaderTitle>
				</PageHeaderContent>
			</PageHeader>

			<RiserStackForm voiceParts={voiceParts} singers={singers} ensembles={ensembles} />
		</>
	);
};

Create.layout = page => <TenantLayout children={page} />;

export default Create;
