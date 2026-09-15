import React, { Fragment, useState } from 'react';
import TenantLayout from '../../Layouts/TenantLayout';
import {
	PageHeader,
	PageHeaderActions,
	PageHeaderBreadcrumbs,
	PageHeaderContent,
	PageHeaderMeta,
	PageHeaderTitle,
} from '../../components/PageHeader/PageHeader';
import VoicePartTag from '../../components/VoicePartTag';
import SingerStatusTag from '../../components/SingerStatusTag';
import ButtonLink from '../../components/inputs/ButtonLink';
import Dialog from '../../components/Dialog';
import RadioGroup from '../../components/inputs/RadioGroup';
import { usePage } from '@inertiajs/react';
import AppHead from '../../components/AppHead';
import Icon from '../../components/Icon';
import DateTag from '../../components/DateTag';
import DeleteDialog from '../../components/DeleteDialog';
import Pronouns from '../../components/Pronouns';
import SingerStatus from '../../SingerStatus';
import SectionLayout from '../../components/SectionLayout';
import useRoute from '../../hooks/useRoute';
import { PersonalDetailsSection } from './sections/PersonalDetailsSection';
import { MembershipDetailsSection } from './sections/MembershipDetailsSection';
import { VoicePlacementSection } from './sections/VoicePlacementSection';
import { EnrolmentDetailsSection } from './sections/EnrolmentDetailsSection';
import { OnboardingTaskSection } from './sections/OnboardingTaskSection';
import CustomFieldsSection from './sections/CustomFieldsSection';
import SingerAttendanceSummary from '../../components/Attendance/SingerAttendanceSummary';
import SingerRsvpSummary from '../../components/Attendance/SingerRsvpSummary';
import { usePhoneBreadcrumb } from '../../lib/reactNative';
import Button from '../../components/inputs/Button';
import ActionMenuItem from '../../components/ActionMenu/ActionMenuItem';
import PageTopBar, { PageActionsMenu, PageTopNavigation } from '../../components/PageTopBar';

const Show = ({
	singer,
	attendanceSummary,
	rsvpSummary,
	statuses,
	voiceParts,
	ensemblesNotEnrolled,
	customFields,
	performanceTypeId,
	setSidebarOpen,
}) => {
	const [deleteDialogIsOpen, setDeleteDialogIsOpen] = useState(false);
	const [moveDialogIsOpen, setMoveDialogIsOpen] = useState(false);
	const { can, user: authUser } = usePage().props;
	const { route } = useRoute();

	usePhoneBreadcrumb(singer.user.name, [{ name: 'Singers', url: route('singers.index') }]);

	const filteredActions = [
		{
			label: 'Edit Profile',
			icon: 'user-edit',
			url: route('account.edit'),
			can: singer.user.id === authUser.id,
			variant: 'primary',
		},
		{
			label: 'Edit Membership',
			icon: 'edit',
			url: route('singers.edit', { singer }),
			can: 'update_singer',
			variant: 'primary',
		},
		{
			label: 'Move',
			icon: 'arrow-circle-right',
			onClick: () => setMoveDialogIsOpen(true),
			can: 'update_singer',
		},
		{
			label: 'Delete',
			icon: 'trash',
			onClick: () => setDeleteDialogIsOpen(true),
			variant: 'danger-outline',
			can: 'delete_singer',
		},
	]
		.filter(action => action.can === true || singer.can[action.can])
		.filter(action => !!action);

	const breadcrumbs = [
		{ name: 'Singers', url: route('singers.index') },
		{ name: singer.user.name, url: route('singers.show', { singer }) },
	];

	return (
		<>
			<AppHead title={`${singer.user.name} - Singers`} />

			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<PageTopNavigation breadcrumbs={breadcrumbs}></PageTopNavigation>

				<PageActionsMenu>
					{filteredActions.map((action, key) => (
						<ActionMenuItem
							key={key}
							url={action.url}
							onClick={action.onClick}
							download={action.download}
							variant={action.variant}
							method={action.method}
							disabled={action.disabled}
						>
							<Icon icon={action.icon} mr />

							{action.label}
						</ActionMenuItem>
					))}
				</PageActionsMenu>
			</PageTopBar>

			<PageHeader>
				<div className="flex sm:items-center">
					{singer.user.profile_avatar_url && (
						<img
							src={singer.user.profile_avatar_url}
							alt={singer.user.name}
							className="h-32 rounded-md mb-3 lg:mb-0 mr-6"
						/>
					)}
					<PageHeaderContent>
						<PageHeaderBreadcrumbs breadcrumbs={breadcrumbs} />
						<PageHeaderTitle>
							<span className="flex flex-col lg:flex-row lg:items-center gap-x-1.5">
								{singer.user.name}{' '}
								{singer.user.pronouns && <Pronouns pronouns={singer.user.pronouns} />}
							</span>
						</PageHeaderTitle>

						<PageHeaderMeta>
							{singer.enrolments.length === 1 && singer.enrolments?.[0]?.voice_part && (
								<div>
									<VoicePartTag
										key={singer.enrolments[0].id}
										title={singer.enrolments[0].voice_part.title}
										colour={singer.enrolments[0].voice_part.colour}
									/>
								</div>
							)}
							<SingerStatusTag status={new SingerStatus(singer.status.status)} withLabel />
							<DateTag date={singer.joined_at} label="Joined" className="hidden lg:block" />
						</PageHeaderMeta>
					</PageHeaderContent>
				</div>
				<PageHeaderActions>
					{/* Desktop */}
					{filteredActions.map((action, key) => (
						<Button
							key={action.label}
							href={action.url}
							onClick={action.onClick}
							size="sm"
							variant={action.variant}
							external={action.download}
							download={action.download}
							method={action.method}
							disabled={action.disabled}
						>
							<Icon icon={action.icon} mr />
							{action.label}
						</Button>
					))}
				</PageHeaderActions>
			</PageHeader>

			<DeleteDialog
				title="Delete Singer"
				url={route('singers.destroy', { singer })}
				isOpen={deleteDialogIsOpen}
				setIsOpen={setDeleteDialogIsOpen}
			>
				Are you sure you want to deactivate this singer? All of their data will be permanently removed from our
				servers forever. This action cannot be undone.
			</DeleteDialog>

			<MoveSingerDialog
				isOpen={moveDialogIsOpen}
				setIsOpen={setMoveDialogIsOpen}
				singer={singer}
				statuses={statuses}
			/>

			<SectionLayout
				layout={{
					className:
						'grid-cols-1 divide-y divide-gray-300 sm:grid sm:grid-cols-3 sm:divide-x sm:divide-y-0 xl:grid-cols-4',
					columns: [
						{ id: 'main', className: 'sm:col-span-2 xl:col-span-3 divide-y divide-gray-300' },
						{ id: 'aside', className: 'sm:col-span-1 divide-y divide-gray-300' },
					],
				}}
				sections={[
					{
						id: 'about',
						column: 'main',
						title: 'About',
						show: true,
						content: <PersonalDetailsSection singer={singer} />,
					},
					{
						id: 'membership',
						column: 'main',
						title: 'Membership',
						show: true,
						content: <MembershipDetailsSection singer={singer} can={can} />,
					},
					{
						id: 'enrolments',
						column: 'main',
						title: 'Enrolments',
						show: true,
						content: (
							<EnrolmentDetailsSection
								singer={singer}
								voiceParts={voiceParts}
								ensembles={ensemblesNotEnrolled}
							/>
						),
					},
					{
						id: 'custom-fields',
						column: 'main',
						title: 'Custom Fields',
						show: can['list_custom_field_entries'],
						content: <CustomFieldsSection singer={singer} customFields={customFields} />,
					},
					{
						id: 'attendance',
						column: 'aside',
						title: 'Attendance',
						show: singer.can['view_attendance'],
						content: (
							<>
								<div className="py-4 px-4 lg:px-8 bg-gray-50">
									<SingerAttendanceSummary attendanceSummary={attendanceSummary} />
								</div>
								<hr className="border-gray-200" />
								<div className="py-4 px-4 lg:px-8 bg-gray-50">
									<SingerRsvpSummary
										rsvpSummary={rsvpSummary}
										performanceTypeId={performanceTypeId}
									/>
								</div>
							</>
						),
					},
					{
						id: 'onboarding',
						column: 'aside',
						title: 'Onboarding',
						show: can['list_tasks'] && singer.onboarding_enabled === 1,
						content: <OnboardingTaskSection singer={singer} />,
					},
					{
						id: 'musicianship',
						column: 'aside',
						title: 'Musicianship',
						action: singer.placement ? <EditSingerPlacementButton singer={singer} /> : null,
						show: singer.can['create_placement'],
						content: <VoicePlacementSection singer={singer} />,
					},
				]}
			/>
		</>
	);
};

Show.layout = page => <TenantLayout children={page} />;

export default Show;

const EditSingerPlacementButton = ({ singer }) => {
	const { route } = useRoute();

	return (
		<ButtonLink
			variant="primary"
			size="xs"
			href={route('singers.placements.edit', { singer: singer.id, placement: singer.placement.id })}
		>
			<Icon icon="edit" />
			Edit
		</ButtonLink>
	);
};

const MoveSingerDialog = ({ isOpen, setIsOpen, singer, statuses }) => {
	const { route } = useRoute();

	const [selectedStatus, setSelectedStatus] = useState(statuses.find(status => status.slug === singer.status) ?? 0);

	return (
		<Dialog
			title="Move Singer"
			okLabel="Move"
			okUrl={route('singers.statuses.update', { singer })}
			okVariant="primary"
			okMethod="get"
			data={{ move_status: selectedStatus.id }}
			isOpen={isOpen}
			setIsOpen={setIsOpen}
		>
			<p className="mb-2">
				Are you sure you want to move this singer? This will move them to the selected status. This can be
				undone, however, it may trigger some onboarding emails, which cannot be undone.
			</p>
			<RadioGroup
				label="Select a new status"
				options={statuses.map(status => ({
					id: status,
					name: SingerStatus.statuses[status.slug].title,
					colour: SingerStatus.statuses[status.slug].textColour,
					icon: SingerStatus.statuses[status.slug].icon,
				}))}
				selected={selectedStatus}
				setSelected={setSelectedStatus}
				vertical
			/>
		</Dialog>
	);
};
