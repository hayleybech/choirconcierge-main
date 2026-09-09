import { PageHeader2 } from '../../../components/PageHeader/PageHeader';
import Breadcrumbs from '../../../components/PageHeader/Breadcrumbs';
import { PageHeading } from '../../../components/PageHeader/PageHeading';
import PageTopBar, { PageTopBarTitle, PageTopNavigation } from '../../../components/PageTopBar';
import Icon from '../../../components/Icon';
import TenantLayout from '../../../Layouts/TenantLayout';
import React from 'react';
import PlacementForm from './PlacementForm';
import AppHead from '../../../components/AppHead';
import useRoute from '../../../hooks/useRoute';

const Create = ({ singer, voice_parts, setSidebarOpen }) => {
	const { route } = useRoute();

	return (
		<>
			<AppHead title={`Add Voice Placement - ${singer.user.name}`} />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<PageTopNavigation
					backUrl={route('singers.show', { singer })}
					breadcrumbs={[
						{ name: 'Singers', url: route('singers.index') },
						{ name: singer.user.name, url: route('singers.show', { singer }) },
					]}
				>
					<PageTopBarTitle title="Create Placement" />
				</PageTopNavigation>
			</PageTopBar>
			<PageHeader2>
				<div className="lg:flex lg:items-center lg:justify-between">
					<div className="flex-1 min-w-0">
						<div className="hidden lg:block">
							<Breadcrumbs
								breadcrumbs={[
									{ name: 'Singers', url: route('singers.index') },
									{ name: singer.user.name, url: route('singers.show', { singer }) },
									{
										name: 'Create Voice Placement',
										url: route('singers.placements.create', { singer }),
									},
								]}
								showLastChevron={false}
							/>
						</div>
						<PageHeading>
							<Icon icon="users" type="solid" className="mr-2" />
							Create Placement
						</PageHeading>
					</div>
				</div>
			</PageHeader2>

			<PlacementForm singer={singer} voice_parts={voice_parts} />
		</>
	);
};

Create.layout = page => <TenantLayout children={page} />;

export default Create;
