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
import MailingListForm from './MailingListForm';
import useRoute from '../../hooks/useRoute';
import TrialAntiSpamNotice from './TrialAntiSpamNotice';

const Edit = ({ list, roles, voiceParts, singerStatuses, ensembles, setSidebarOpen }) => {
	const { route } = useRoute();

	const breadcrumbs = [
		{ name: 'Communications', url: route('communications.index') },
		{ name: 'Mailing Lists', url: route('groups.index') },
		{ name: list.title, url: route('groups.show', { group: list }) },
		{ name: 'Edit Mailing List', url: route('groups.edit', { group: list }) },
	];

	return (
		<>
			<AppHead title={`Edit - ${list.title}`} />

			<TrialAntiSpamNotice />

			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<PageTopNavigation breadcrumbs={breadcrumbs}></PageTopNavigation>
			</PageTopBar>
			<PageHeader>
				<PageHeaderContent>
					<PageHeaderBreadcrumbs breadcrumbs={breadcrumbs} />
					<PageHeaderTitle>
						<Icon icon="mail-bulk" type="solid" className="mr-2" />
						Edit Mailing List
					</PageHeaderTitle>
				</PageHeaderContent>
			</PageHeader>

			<MailingListForm
				list={list}
				roles={roles}
				voiceParts={voiceParts}
				singerStatuses={singerStatuses}
				ensembles={ensembles}
			/>
		</>
	);
};

Edit.layout = page => <TenantLayout children={page} />;

export default Edit;
