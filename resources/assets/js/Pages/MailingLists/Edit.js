import React from 'react';
import TenantLayout from '../../Layouts/TenantLayout';
import { PageHeader2 } from '../../components/PageHeader/PageHeader';
import Breadcrumbs from '../../components/PageHeader/Breadcrumbs';
import { PageHeading } from '../../components/PageHeader/PageHeading';
import PageTopBar, { PageTopBarTitle, PageTopNavigation } from '../../components/PageTopBar';
import Icon from '../../components/Icon';
import AppHead from '../../components/AppHead';
import MailingListForm from './MailingListForm';
import useRoute from '../../hooks/useRoute';
import TrialAntiSpamNotice from './TrialAntiSpamNotice';

const Edit = ({ list, roles, voiceParts, singerStatuses, ensembles, setSidebarOpen }) => {
	const { route } = useRoute();

	return (
		<>
			<AppHead title={`Edit - ${list.title}`} />

			<TrialAntiSpamNotice />

			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<PageTopNavigation
					backUrl={route('groups.show', { group: list })}
					breadcrumbs={[
						{ name: 'Communications', url: route('communications.index') },
						{ name: 'Mailing Lists', url: route('groups.index') },
						{ name: list.title, url: route('groups.show', { group: list }) },
					]}
				>
					<PageTopBarTitle title="Edit Mailing List" />
				</PageTopNavigation>
			</PageTopBar>
			<PageHeader2>
				<div className="lg:flex lg:items-center lg:justify-between">
					<div className="flex-1 min-w-0">
						<div className="hidden lg:block">
							<Breadcrumbs
								breadcrumbs={[
									{ name: 'Communications', url: route('communications.index') },
									{ name: 'Mailing Lists', url: route('groups.index') },
									{ name: list.title, url: route('groups.show', { group: list }) },
									{ name: 'Edit', url: route('groups.edit', { group: list }) },
								]}
								showLastChevron={false}
							/>
						</div>
						<PageHeading>
							<Icon icon="mail-bulk" type="solid" className="mr-2" />
							Edit Mailing List
						</PageHeading>
					</div>
				</div>
			</PageHeader2>

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
