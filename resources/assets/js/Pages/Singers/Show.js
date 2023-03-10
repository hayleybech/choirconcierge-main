import React, {useState} from 'react'
import TenantLayout from "../../Layouts/TenantLayout";
import PageHeader from "../../components/PageHeader";
import VoicePartTag from "../../components/VoicePartTag";
import SingerCategoryTag from "../../components/SingerCategoryTag";
import ButtonLink from "../../components/inputs/ButtonLink";
import classNames from "../../classNames";
import Dialog from "../../components/Dialog";
import RadioGroup from "../../components/inputs/RadioGroup";
import {usePage} from "@inertiajs/inertia-react";
import AppHead from "../../components/AppHead";
import Badge from "../../components/Badge";
import Icon from "../../components/Icon";
import DateTag from "../../components/DateTag";
import DeleteDialog from "../../components/DeleteDialog";
import Pronouns from "../../components/Pronouns";
import SingerStatus from "../../SingerStatus";
import CollapsePanel from "../../components/CollapsePanel";
import CollapseGroup from "../../components/CollapseGroup";
import FeeStatus from "../../components/FeeStatus";
import useRoute from "../../hooks/useRoute";

const Progress = ({ value, max, min }) => (
    <div className="flex items-center text-xs">
        {min}
        <div className="grow mx-2">
            <div className="h-5 bg-purple-100 border border-purple-300 rounded-sm overflow-hidden">
                <div className="bg-purple-600 h-full flex justify-center items-center text-white" style={{ width: `${value / max * 100}%`}}>
                    {value}
                </div>
            </div>
        </div>
        {max}
    </div>
);

const Range = ({ value, min, max, minLabel, maxLabel }) => (
    <div className="flex items-center text-xs">
        {minLabel}
        <div className="grow mx-2">
            <div className="h-1 bg-gray-200 rounded-sm flex items-center pr-3">
                <div className="w-full relative">
                    <div
                        className="bg-gray-600 h-3 w-3 flex justify-center items-center text-white relative rounded-full"
                        style={{ left: `${value / max * 100}%`}}
                    />
                </div>
            </div>
        </div>
        {maxLabel}
    </div>
);

const DetailList = ({ items, gridCols = 'sm:grid-cols-2 md:grid-cols-4' }) => (
    <dl className={classNames("grid grid-cols-1 gap-x-4 gap-y-8", gridCols)}>
        {items.map(({ label, value, colClass = "sm:col-span-1" }) => (
            <div key={label} className={colClass}>
                <dt className="text-sm font-medium text-gray-500">
                    {label}
                </dt>
                <dd className="mt-1 text-sm text-gray-900">
                    {value}
                </dd>
            </div>
        ))}
    </dl>
);

const MoveSingerDialog = ({ isOpen, setIsOpen, singer, categories }) => {
    const { route } = useRoute();

    const [selectedCategory, setSelectedCategory] = useState(singer.category.id ?? 0);

    return (
        <Dialog
            title="Move Singer"
            okLabel="Move"
            okUrl={route('singers.categories.update', {singer})}
            okVariant="primary"
            okMethod="get"
            data={{ move_category: selectedCategory.id }}
            isOpen={isOpen}
            setIsOpen={setIsOpen}
        >
            <p className="mb-2">
                Are you sure you want to move this singer?
                This will move them to the selected stage of your onboarding process.
                This can be undone, however, it may trigger some onboarding emails, which cannot be undone.
            </p>
            <RadioGroup
                label="Select a new category"
                options={categories.map(category => ({
                    id: category,
                    name: SingerStatus.statuses[category.slug].title,
                    textColour: SingerStatus.statuses[category.slug].textColour,
                    icon: SingerStatus.statuses[category.slug].icon,
                }))}
                selected={selectedCategory}
                setSelected={setSelectedCategory}
                vertical
            />
        </Dialog>
    );
}

const Show = ({ singer, categories }) => {
    const [deleteDialogIsOpen, setDeleteDialogIsOpen] = useState(false);
    const [moveDialogIsOpen, setMoveDialogIsOpen] = useState(false);
    const { can, user: authUser } = usePage().props;
    const { route } = useRoute();

    return (
        <>
            <AppHead title={`${singer.user.name} - Singers`} />
            <PageHeader
                title={<>{singer.user.name} {singer.user.pronouns && <Pronouns pronouns={singer.user.pronouns} />}</>}
                image={singer.user.profile_avatar_url}
                meta={[
                    <>{singer.voice_part && <VoicePartTag title={singer.voice_part.title} colour={singer.voice_part.colour} />}</>,
                    <SingerCategoryTag status={new SingerStatus(singer.category.slug)} withLabel />,
                    <DateTag date={singer.joined_at} label="Joined" />,
                ]}
                breadcrumbs={[
                    { name: 'Dashboard', url: route('dash')},
                    { name: 'Singers', url: route('singers.index')},
                    { name: singer.user.name, url: route('singers.show', {singer}) },
                ]}
                actions={[
                    { label: 'Edit Profile', icon: 'user-edit', url: route('accounts.edit'), can: singer.user.id === authUser.id, variant: 'primary' },
                    { label: 'Edit Membership', icon: 'edit', url: route('singers.edit', {singer}), can: 'update_singer', variant: 'primary' },
                    { label: 'Move', icon: 'arrow-circle-right', onClick: () => setMoveDialogIsOpen(true), can: 'update_singer' },
                    { label: 'Delete', icon: 'trash', onClick: () => setDeleteDialogIsOpen(true), variant: 'danger-outline', can: 'delete_singer' },
                ].filter(action => action.can === true || singer.can[action.can])}
            />

            <DeleteDialog
                title="Delete Singer"
                url={route('singers.destroy', {singer})}
                isOpen={deleteDialogIsOpen}
                setIsOpen={setDeleteDialogIsOpen}
            >
                Are you sure you want to deactivate this singer?
                All of their data will be permanently removed from our servers forever.
                This action cannot be undone.
            </DeleteDialog>

            <MoveSingerDialog isOpen={moveDialogIsOpen} setIsOpen={setMoveDialogIsOpen} singer={singer} categories={categories} />

            <div className="grid grid-cols-1 sm:grid-cols-3 xl:grid-cols-4 divide-y divide-gray-300 sm:divide-y-0 sm:divide-x">

                <div className="sm:col-span-2 xl:col-span-3 divide-y divide-y-gray-300">
                    <CollapseGroup items={[
                        {
                            title: 'Personal Details',
                            show: true,
                            defaultOpen: true,
                            content: <PersonalDetails singer={singer} />,
                        },
                        {
                            title: 'Membership Details',
                            show: true,
                            defaultOpen: true,
                            content: <MembershipDetails singer={singer} />,
                        },
                    ]} />
                </div>

                <div className="sm:col-span-1 divide-y divide-y-gray-300">
                    <CollapseGroup items={[
                        {
                            title:
                                <div className="inline-flex flex-wrap items-baseline">
                                    Onboarding
                                    <p className="ml-2 text-sm text-gray-500 truncate">{singer.onboarding_enabled ? 'Enabled' : 'Disabled'}</p>
                                </div>,
                            show: can['list_tasks'],
                            content: <TaskList singer={singer}/>,
                        },
                        {
                            title: 'Voice Placement',
                            action: singer.placement ? <EditSingerPlacementButton singer={singer} /> : null,
                            show: singer.can['create_placement'],
                            content: <VoicePlacementDetails singer={singer} />,
                        },
                    ]} />
                </div>

            </div>
        </>
    );
}

Show.layout = page => <TenantLayout children={page} />

export default Show;

const TaskList = ({ singer }) => {
    const { route } = useRoute();

    return (
        <CollapsePanel>
            <nav className="flex" aria-label="Progress">
                <ol role="list" className="space-y-6">
                    {singer.tasks.map((task, index, tasks) => (
                        <li key={index}>
                        <span className="flex items-center">
                            <span className="shrink-0 h-5 w-5 relative flex items-center justify-center" aria-hidden="true">
                                {task.pivot.completed
                                && <Icon icon="check-circle" className="text-purple-500 text-sm" />
                                || (! tasks[index - 1] || tasks[index - 1].pivot.completed) && <>
                                    <span className="absolute h-4 w-4 rounded-full bg-purple-200" />
                                    <span className="relative block w-2 h-2 bg-purple-600 rounded-full" />
                                </>
                                || <div className="h-2 w-2 bg-gray-300 rounded-full group-hover:bg-gray-400" />
                                }
                            </span>
                            {task.pivot.completed
                            && <span className="ml-3 text-sm font-medium text-gray-500 group-hover:text-gray-900">{task.name}</span>
                            || (! tasks[index - 1] || tasks[index - 1].pivot.completed) && <>
                                <span className="ml-3 text-sm font-medium text-purple-600">{task.name}</span>
                                {task.can['complete'] && (
                                    <ButtonLink href={route(task.route, {singer: singer.id, task: task.id})} size="xs" className="ml-3">
                                        Complete
                                    </ButtonLink>
                                )}
                            </>
                            || <span className="ml-3 text-sm font-medium text-gray-500 group-hover:text-gray-900">{task.name}</span>
                            }
                        </span>
                        </li>
                    ))}
                </ol>
            </nav>
        </CollapsePanel>
    );
}

const PersonalDetails = ({ singer }) => (
    <CollapsePanel>
        <DetailList items={[
            {
                label: 'Contact Details',
                value: <>
                    <p>
                        <Icon icon="envelope" mr type="regular" className="text-gray-400" />
                        <a href={`mailto:${singer.user.email}`} target="_blank">{singer.user.email}</a>
                    </p>
                    <p>
                        <Icon icon="phone" mr type="regular" className="text-gray-400" />
                        {singer.user.phone ? <a href={`tel:${singer.user.phone}`} target="_blank">{singer.user.phone}</a> : 'No phone'}
                    </p>
                </>,
                colClass: 'sm:col-span-2 xl:col-span-1',
            },
            {
                label: 'Date of Birth',
                value: <>{singer.user.dob ? <DateTag date={singer.user.dob} /> : 'No date of birth'}</>,
            },
            {
                label: 'Height',
                value: singer.user.height ? `${Math.round(singer.user.height)} cm` : 'Unknown',
            },
            {
                label: 'BHA Member ID',
                value: <>
                    <Icon icon="id-card" mr type="regular" className="text-gray-400" />
                    <span>{singer.user.bha_id ?? 'Unknown'}</span>
                    <span className="ml-2 text-gray-500">{`(${singer.user.bha_type})` ?? ''}</span>
                </>
            },
            {
                label: 'Address',
                value: <>
                    {singer.user.address_street_1
                        ? (<>
                            {singer.user.address_street_1}<br />
                            {singer.user.address_street_2 && (<>{singer.user.address_street_2}<br /></>)}
                            {`${singer.user.address_suburb}, ${singer.user.address_state} ${singer.user.address_postcode}`}
                        </>)
                        : 'No address'
                    }
                </>,
            },
            {
                label: 'Profession',
                value: singer.user.profession ?? 'None listed',
            },
            {
                label: 'Other Skills',
                value: singer.user.skills ?? 'None listed',
            },
            {
                label: 'Emergency Contact',
                value: <>
                    <p>
                        <Icon icon="user" mr type="regular" className="text-gray-400" />
                        {singer.user.ice_name ?? 'No emergency contact'}
                    </p>
                    <p>
                        <Icon icon="phone" mr type="regular" className="text-gray-400" />
                        {singer.user.ice_phone ? <a href={`tel:${singer.user.ice_phone}`} target="_blank">{singer.user.ice_phone}</a> : 'No phone'}
                    </p>
                </>,
                colClass: 'sm:col-span-2 xl:col-span-1',
            },
        ]}/>
    </CollapsePanel>
);

const MembershipDetails = ({ singer }) => (
    <CollapsePanel>
        <DetailList items={[
            {
                label: 'Roles',
                value: (
                    <div className="space-x-1.5 space-y-1.5">
                        {singer.roles.map(role => <Badge key={role.name}>{role.name.split(' ')[0]}</Badge>)}
                    </div>
                ),
                colClass: 'sm:col-span-2',
            },
            {
                label: 'Membership Fees',
                value: <>
                    <FeeStatus status={singer.fee_status} />
                    {singer.paid_until && (
                        <span className="text-sm text-gray-500 italic">
                            <DateTag date={singer.paid_until} label="Expires" />
                        </span>
                    )}
                </>
            },
            {
                label: 'Referred by',
                value: singer.referrer ?? 'Unknown',
            },
            {
                label: 'Notes / Membership Details',
                value: singer.membership_details ?? 'N/A',
                colClass: 'sm:col-span-2',
            },
            {
                label: 'Member Since',
                value: <>
                    <DateTag date={singer.joined_at} /><br />
                    <span className="text-sm text-gray-500 italic">
                        <DateTag date={singer.created_at} label="Added" />
                    </span>
                </>,
            },
            {
                label: 'Last Login',
                value: <DateTag date={singer.user.last_login} />,
            },
            {
                label: 'Reason for Joining',
                value: singer.reason_for_joining ?? 'Unknown',
            },
        ]} />
    </CollapsePanel>
);

const EditSingerPlacementButton = ({ singer }) => {
    const { route } = useRoute();

    return (
        <ButtonLink
            variant="primary"
            size="sm"
            href={route('singers.placements.edit', {singer: singer.id, placement: singer.placement.id})}
        >
            <Icon icon="edit" />
            Edit
        </ButtonLink>
    );
}

const VoicePlacementDetails = ({ singer }) =>
    singer.placement
        ? (
            <CollapsePanel>
                <DetailList gridCols="sm:grid-cols-2" items={[
                    {
                        label: 'Voice Part',
                        value: <VoicePartTag title={singer.voice_part.title} colour={singer.voice_part.colour} />,
                    },
                    {
                        label: 'Voice Tone',
                        value: <Range
                            min={1}
                            max={3}
                            minLabel={<Icon icon="flute" className="text-gray-600 fa-lg" />}
                            maxLabel={<Icon icon="trumpet" className="text-gray-600 fa-lg" />}
                            value={singer.placement.voice_tone}
                        />,
                    },
                    {
                        label: 'Pitch Skill',
                        value: <Progress value={singer.placement.skill_pitch} min={1} max={5} />,
                    },
                    {
                        label: 'Harmony Skill',
                        value: <Progress value={singer.placement.skill_harmony} min={1} max={5} />,
                    },
                    {
                        label: 'Performance Skill',
                        value: <Progress value={singer.placement.skill_performance} min={1} max={5} />,
                    },
                    {
                        label: 'Sight Reading Skill',
                        value: <Progress value={singer.placement.skill_sightreading} min={1} max={5} />,
                    },
                    {
                        label: 'Experience',
                        value: singer.placement.experience ?? 'None listed',
                    },
                    {
                        label: 'Instruments',
                        value: singer.placement.instruments ?? 'None listed',
                    },
                ]} />
            </CollapsePanel>
        )
        : <VoicePlacementEmptyState singer={singer} />
;

const VoicePlacementEmptyState = ({ singer }) => {
    const { route } = useRoute();

    return (
        <div className="text-center py-6 px-4">
            <Icon icon="user-music" type="light" className="text-gray-400 text-4xl mb-2" />
            <h3 className="mt-2 text-sm font-medium text-gray-900">No Voice Placement</h3>
            <p className="mt-1 text-sm text-gray-500">
                Get this singer started on their journey by creating their Voice Placement.
            </p>
            <div className="mt-6">
                <ButtonLink href={route('singers.placements.create', {singer})} variant="primary">
                    <Icon icon="plus" />
                    Create Placement
                </ButtonLink>
            </div>
        </div>
    );
}