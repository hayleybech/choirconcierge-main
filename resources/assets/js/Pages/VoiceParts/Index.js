import React from 'react';
import TenantLayout from '../../Layouts/TenantLayout';
import {
	PageHeader,
	PageHeaderActions,
	PageHeaderBreadcrumbs,
	PageHeaderContent,
	PageHeaderTitle,
} from '../../components/PageHeader/PageHeader';
import PageTopBar, { PageActionsMenu, PageTopNavigation } from '../../components/PageTopBar';
import ActionMenuItem from '../../components/ActionMenu/ActionMenuItem';
import Icon from '../../components/Icon';
import Button from '../../components/inputs/Button';
import AppHead from '../../components/AppHead';
import VoicePartTableDesktop from './VoicePartTableDesktop';
import VoicePartTableMobile from './VoicePartTableMobile';
import { usePage } from '@inertiajs/react';
import useRoute from '../../hooks/useRoute';

const Index = ({ parts, setSidebarOpen }) => {
	const { can } = usePage().props;
	const { route } = useRoute();

	const breadcrumbs = [
		{ name: 'Singers', url: route('singers.index') },
		{ name: 'Voice Parts', url: route('voice-parts.index') },
	];

	return (
		<>
			<AppHead title="Voice Parts" />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
					<PageTopNavigation breadcrumbs={breadcrumbs}></PageTopNavigation>
					{can.create_voice_part && (
						<PageActionsMenu>
							<ActionMenuItem url={route('voice-parts.create')} variant="primary">
								<Icon icon="plus" mr />
								Add New
							</ActionMenuItem>
						</PageActionsMenu>
					)}
			</PageTopBar>
			<PageHeader>
				<PageHeaderContent>
					<PageHeaderBreadcrumbs breadcrumbs={breadcrumbs} />
					<PageHeaderTitle>
						<Icon icon="users-class" type="solid" className="mr-2" />
						Voice Parts
					</PageHeaderTitle>
				</PageHeaderContent>
				<PageHeaderActions>
					{can.create_voice_part && (
						<Button href={route('voice-parts.create')} size="sm" variant="primary">
							<Icon icon="plus" mr />
							Add New
						</Button>
					)}
				</PageHeaderActions>
			</PageHeader>

			{/* Desktop Table */}
			<div className="hidden lg:flex flex-col">
				<VoicePartTableDesktop voiceParts={parts} />
			</div>

			{/* Mobile Table */}
			<div className="bg-white shadow block lg:hidden">
				<VoicePartTableMobile voiceParts={parts} />
			</div>
		</>
	);
};

Index.layout = page => <TenantLayout children={page} />;

export default Index;
