import React from 'react';
import TenantLayout from '../../Layouts/TenantLayout';
import { PageHeader2 } from '../../components/PageHeader/PageHeader';
import Breadcrumbs from '../../components/PageHeader/Breadcrumbs';
import { PageHeading } from '../../components/PageHeader/PageHeading';
import PageTopBar, { PageTopBarTitle, PageTopNavigation } from '../../components/PageTopBar';
import Icon from '../../components/Icon';
import AppHead from '../../components/AppHead';
import RiserStackForm from './RiserStackForm';
import useRoute from '../../hooks/useRoute';

const Create = ({ voiceParts, singers, ensembles, setSidebarOpen }) => {
	const { route } = useRoute();

	return (
		<>
			<AppHead title="Create Riser Stack" />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<PageTopNavigation
					backUrl={route('stacks.index')}
					breadcrumbs={[{ name: 'Riser Stacks', url: route('stacks.index') }]}
				>
					<PageTopBarTitle title="Create Riser Stack" />
				</PageTopNavigation>
			</PageTopBar>
			<PageHeader2>
				<div className="lg:flex lg:items-center lg:justify-between">
					<div className="flex-1 min-w-0">
						<div className="hidden lg:block">
							<Breadcrumbs
								breadcrumbs={[
									{ name: 'Riser Stacks', url: route('stacks.index') },
									{ name: 'Create', url: route('stacks.create') },
								]}
								showLastChevron={false}
							/>
						</div>
						<PageHeading>
							<Icon icon="people-arrows" type="solid" className="mr-2" />
							Create Riser Stack
						</PageHeading>
					</div>
				</div>
			</PageHeader2>

			<RiserStackForm voiceParts={voiceParts} singers={singers} ensembles={ensembles} />
		</>
	);
};

Create.layout = page => <TenantLayout children={page} />;

export default Create;
