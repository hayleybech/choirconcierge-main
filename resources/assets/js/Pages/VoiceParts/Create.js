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
import VoicePartForm from './VoicePartForm';
import useRoute from '../../hooks/useRoute';

const Create = ({ setSidebarOpen }) => {
	const { route } = useRoute();

	const breadcrumbs = [
		{ name: 'Singers', url: route('singers.index') },
		{ name: 'Voice Parts', url: route('voice-parts.index') },
		{ name: 'Create Voice Part', url: route('voice-parts.create') },
	];

	return (
		<>
			<AppHead title="Create Voice Part" />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<PageTopNavigation breadcrumbs={breadcrumbs}></PageTopNavigation>
			</PageTopBar>
			<PageHeader>
				<PageHeaderContent>
					<PageHeaderBreadcrumbs breadcrumbs={breadcrumbs} />
					<PageHeaderTitle>
						<Icon icon="users-class" type="solid" className="mr-2" />
						Create Voice Part
					</PageHeaderTitle>
				</PageHeaderContent>
			</PageHeader>

			<VoicePartForm />
		</>
	);
};

Create.layout = page => <TenantLayout children={page} />;

export default Create;
