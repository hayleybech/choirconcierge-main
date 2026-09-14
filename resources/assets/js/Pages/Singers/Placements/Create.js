import {
	PageHeader,
	PageHeaderBreadcrumbs,
	PageHeaderContent,
	PageHeaderTitle,
} from '../../../components/PageHeader/PageHeader';
import PageTopBar, { PageTopNavigation } from '../../../components/PageTopBar';
import Icon from '../../../components/Icon';
import TenantLayout from '../../../Layouts/TenantLayout';
import React from 'react';
import PlacementForm from './PlacementForm';
import AppHead from '../../../components/AppHead';
import useRoute from '../../../hooks/useRoute';

const Create = ({ singer, voice_parts, setSidebarOpen }) => {
	const { route } = useRoute();

	const breadcrumbs = [
		{ name: 'Singers', url: route('singers.index') },
		{ name: singer.user.name, url: route('singers.show', { singer }) },
		{
			name: 'Create Placement',
			url: route('singers.placements.create', { singer }),
		},
	];

	return (
		<>
			<AppHead title={`Add Voice Placement - ${singer.user.name}`} />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<PageTopNavigation breadcrumbs={breadcrumbs}></PageTopNavigation>
			</PageTopBar>
			<PageHeader>
				<PageHeaderContent>
					<PageHeaderBreadcrumbs breadcrumbs={breadcrumbs} />
					<PageHeaderTitle>
						<Icon icon="users" type="solid" className="mr-2" />
						Create Placement
					</PageHeaderTitle>
				</PageHeaderContent>
			</PageHeader>

			<PlacementForm singer={singer} voice_parts={voice_parts} />
		</>
	);
};

Create.layout = page => <TenantLayout children={page} />;

export default Create;
