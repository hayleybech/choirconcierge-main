import React, { useState } from 'react';
import TenantLayout from '../../Layouts/TenantLayout';
import PageHeader from '../../components/PageHeader/PageHeader';
import VoicePartTag from '../../components/VoicePartTag';
import SingerCategoryTag from '../../components/SingerCategoryTag';
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
import CollapseGroup from '../../components/CollapseGroup';
import useRoute from '../../hooks/useRoute';
import { PersonalDetailsSection } from './sections/PersonalDetailsSection';
import { MembershipDetailsSection } from './sections/MembershipDetailsSection';
import { VoicePlacementSection } from './sections/VoicePlacementSection';
import { EnrolmentDetailsSection } from './sections/EnrolmentDetailsSection';
import { OnboardingTaskSection } from './sections/OnboardingTaskSection';
import CustomFieldsSection from "./sections/CustomFieldsSection";
import AttendanceSection from './sections/AttendanceSection';
import AttendanceSectionLarge from './sections/AttendanceSectionLarge';

const Show = ({ singer, categories, voiceParts, ensemblesNotEnrolled, customFields }) => {
	const [deleteDialogIsOpen, setDeleteDialogIsOpen] = useState(false);
	const [moveDialogIsOpen, setMoveDialogIsOpen] = useState(false);
	const { can, user: authUser } = usePage().props;
	const { route } = useRoute();

	return (
		<>
			<AppHead title={`${singer.user.name} - Singers`} />
			<PageHeader
				title={
					<>
						{singer.user.name} {singer.user.pronouns && <Pronouns pronouns={singer.user.pronouns} />}
					</>
				}
				image={singer.user.profile_avatar_url}
				meta={
					<>
						{singer.enrolments.length === 1 && singer.enrolments?.[0]?.voice_part && (
							<div>
								<VoicePartTag
									key={singer.enrolments[0].id}
									title={singer.enrolments[0].voice_part.title}
									colour={singer.enrolments[0].voice_part.colour}
								/>
							</div>
						)}
						<SingerCategoryTag status={new SingerStatus(singer.category.slug)} withLabel />
						<DateTag date={singer.joined_at} label="Joined" />
					</>
				}
				breadcrumbs={[
					{ name: 'Dashboard', url: route('dash') },
					{ name: 'Singers', url: route('singers.index') },
					{ name: singer.user.name, url: route('singers.show', { singer }) },
				]}
				actions={[
					{
						label: 'Edit Profile',
						icon: 'user-edit',
						url: route('accounts.edit'),
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
				].filter(action => action.can === true || singer.can[action.can])}
			/>

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
				categories={categories}
			/>

			<div className="grid grid-cols-1 sm:grid-cols-3 xl:grid-cols-4 divide-y divide-gray-300 sm:divide-y-0 sm:divide-x">
				<div className="sm:col-span-2 xl:col-span-3 divide-y divide-y-gray-300">
					<CollapseGroup
						items={[
							{
								title: 'Personal Details',
								show: true,
								defaultOpen: true,
								content: <PersonalDetailsSection singer={singer} />,
							},
							{
								title: 'Membership Details',
								show: true,
								defaultOpen: true,
								content: <MembershipDetailsSection singer={singer} />,
							},
							{
								title: 'Enrolments',
								show: true,
								defaultOpen: true,
								content: (
									<EnrolmentDetailsSection
										singer={singer}
										voiceParts={voiceParts}
										ensembles={ensemblesNotEnrolled}
									/>
								),
							},
							{
								title: 'Custom Fields',
								show: can['list_custom_field_entries'],
								defaultOpen: true,
								content: <CustomFieldsSection singer={singer} customFields={customFields} />,
							},
							{
								title: 'Attendance',
								show: true, // @todo self or attendance permission
								defaultOpen: true,
								content: <AttendanceSectionLarge />,
							},
						]}
					/>
				</div>

				<div className="sm:col-span-1 divide-y divide-y-gray-300">
					<CollapseGroup
						items={[
							{
								title: 'Attendance',
								show: true, // @todo self or attendance permission
								content: <AttendanceSection />,
							},
							{
								title: (
									<div className="inline-flex flex-wrap items-baseline">
										Onboarding
										<p className="ml-2 text-sm text-gray-500 truncate">
											{singer.onboarding_enabled ? 'Enabled' : 'Disabled'}
										</p>
									</div>
								),
								show: can['list_tasks'],
								content: <OnboardingTaskSection singer={singer} />,
							},
							{
								title: 'Voice Placement',
								action: singer.placement ? <EditSingerPlacementButton singer={singer} /> : null,
								show: singer.can['create_placement'],
								content: <VoicePlacementSection singer={singer} />,
							},
						]}
					/>
				</div>
			</div>
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
			size="sm"
			href={route('singers.placements.edit', { singer: singer.id, placement: singer.placement.id })}
		>
			<Icon icon="edit" />
			Edit
		</ButtonLink>
	);
};

const MoveSingerDialog = ({ isOpen, setIsOpen, singer, categories }) => {
	const { route } = useRoute();

	const [selectedCategory, setSelectedCategory] = useState(singer.category.id ?? 0);

	return (
		<Dialog
			title="Move Singer"
			okLabel="Move"
			okUrl={route('singers.categories.update', { singer })}
			okVariant="primary"
			okMethod="get"
			data={{ move_category: selectedCategory.id }}
			isOpen={isOpen}
			setIsOpen={setIsOpen}
		>
			<p className="mb-2">
				Are you sure you want to move this singer? This will move them to the selected stage of your onboarding
				process. This can be undone, however, it may trigger some onboarding emails, which cannot be undone.
			</p>
			<RadioGroup
				label="Select a new category"
				options={categories.map(category => ({
					id: category,
					name: SingerStatus.statuses[category.slug].title,
					colour: SingerStatus.statuses[category.slug].textColour,
					icon: SingerStatus.statuses[category.slug].icon,
				}))}
				selected={selectedCategory}
				setSelected={setSelectedCategory}
				vertical
			/>
		</Dialog>
	);
};