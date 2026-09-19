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

const Edit = ({ singer, placement, voice_parts, setSidebarOpen }) => {
	const { route } = useRoute();

	const breadcrumbs = [
		{ name: 'Singers', url: route('singers.index') },
		{ name: singer.user.name, url: route('singers.show', { singer }) },
		{
			name: 'Edit Placement',
			url: route('singers.placements.edit', { singer, placement }),
		},
	];

	return (
		<>
			<AppHead title={`Edit Voice Placement - ${singer.user.name}`} />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<PageTopNavigation breadcrumbs={breadcrumbs}></PageTopNavigation>
			</PageTopBar>
			<PageHeader>
				<PageHeaderContent>
					<PageHeaderBreadcrumbs breadcrumbs={breadcrumbs} />
					<PageHeaderTitle>
						<Icon icon="users" type="solid" className="mr-2" />
						Edit Placement
					</PageHeaderTitle>
				</PageHeaderContent>
			</PageHeader>

			<PlacementForm singer={singer} placement={placement} voice_parts={voice_parts} />
		</>
	);
};

Edit.layout = page => <TenantLayout children={page} />;

export default Edit;
