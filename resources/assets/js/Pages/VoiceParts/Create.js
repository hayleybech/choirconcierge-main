import React from 'react';
import TenantLayout from '../../Layouts/TenantLayout';
import { PageHeader2 } from '../../components/PageHeader/PageHeader';
import Breadcrumbs from '../../components/PageHeader/Breadcrumbs';
import { PageHeading } from '../../components/PageHeader/PageHeading';
import PageTopBar, { PageTopBarTitle, PageTopNavigation } from '../../components/PageTopBar';
import Icon from '../../components/Icon';
import AppHead from '../../components/AppHead';
import VoicePartForm from './VoicePartForm';
import useRoute from '../../hooks/useRoute';

const Create = ({ setSidebarOpen }) => {
	const { route } = useRoute();

	return (
		<>
			<AppHead title="Create Voice Part" />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<PageTopNavigation
					backUrl={route('voice-parts.index')}
					breadcrumbs={[
						{ name: 'Singers', url: route('singers.index') },
						{ name: 'Voice Parts', url: route('voice-parts.index') },
					]}
				>
					<PageTopBarTitle title="Create Voice Part" />
				</PageTopNavigation>
			</PageTopBar>
			<PageHeader2>
				<div className="lg:flex lg:items-center lg:justify-between">
					<div className="flex-1 min-w-0">
						<div className="hidden lg:block">
							<Breadcrumbs
								breadcrumbs={[
									{ name: 'Singers', url: route('singers.index') },
									{ name: 'Voice Parts', url: route('voice-parts.index') },
									{ name: 'Create', url: route('voice-parts.create') },
								]}
								showLastChevron={false}
							/>
						</div>
						<PageHeading>
							<Icon icon="users-class" type="solid" className="mr-2" />
							Create Voice Part
						</PageHeading>
					</div>
				</div>
			</PageHeader2>

			<VoicePartForm />
		</>
	);
};

Create.layout = page => <TenantLayout children={page} />;

export default Create;
