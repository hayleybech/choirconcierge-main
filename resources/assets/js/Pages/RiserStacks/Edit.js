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

const Edit = ({ stack, voiceParts, singers, ensembles, setSidebarOpen }) => {
	const { route } = useRoute();

	const breadcrumbs = [
		{ name: 'Riser Stacks', url: route('stacks.index') },
		{ name: stack.title, url: route('stacks.show', { stack }) },
		{ name: 'Edit Riser Stack', url: route('stacks.edit', { stack }) },
	];

	return (
		<>
			<AppHead title={`Edit - ${stack.title}`} />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<PageTopNavigation breadcrumbs={breadcrumbs}></PageTopNavigation>
			</PageTopBar>
			<PageHeader>
				<PageHeaderContent>
					<PageHeaderBreadcrumbs breadcrumbs={breadcrumbs} />
					<PageHeaderTitle>
						<Icon icon="people-arrows" type="solid" className="mr-2" />
						Edit Riser Stack
					</PageHeaderTitle>
				</PageHeaderContent>
			</PageHeader>

			<RiserStackForm stack={stack} voiceParts={voiceParts} singers={singers} ensembles={ensembles} />
		</>
	);
};

Edit.layout = page => <TenantLayout children={page} />;

export default Edit;
