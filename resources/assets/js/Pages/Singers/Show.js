import React, {useState} from 'react'
import Layout from "../../Layouts/Layout";
import SingerPageHeader from "./SingerPageHeader";
import VoicePartTag from "../../components/VoicePartTag";
import SingerCategoryTag from "../../components/SingerCategoryTag";
import ButtonLink from "../../components/inputs/ButtonLink";
import {DateTime} from "luxon";
import classNames from "../../classNames";
import Dialog from "../../components/Dialog";
import Button from "../../components/inputs/Button";

const Progress = ({ value, max, min }) => (
    <div className="flex items-center text-xs">
        {min}
        <div className="flex-grow mx-2">
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
        <div className="flex-grow mx-2">
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
            <div className={colClass}>
                <dt className="text-sm font-medium text-gray-500">
                    {label}
                </dt>
                <dd className="mt-1 text-sm text-gray-900">
                    {value}
                </dd>
            </div>
        ))}
    </dl>
)

const Show = ({singer}) => {
    const [deleteDialogIsOpen, setDeleteDialogIsOpen] = useState(false);

    return (
        <>
            <SingerPageHeader
                title={singer.user.name}
                image={singer.user.avatar_url}
                meta={(
                    <>
                        <div className="mt-2 flex items-center text-sm text-gray-500">
                            {singer.voice_part && <VoicePartTag title={singer.voice_part.title} colour={singer.voice_part.colour} />}
                        </div>
                        <div className="mt-2 flex items-center text-sm text-gray-500">
                            <SingerCategoryTag name={singer.category.name} colour={singer.category.colour} withLabel />
                        </div>
                        <div className="mt-2 flex items-center text-sm text-gray-500">
                            <i className="far fa-fw fa-calendar-day mr-1.5 text-gray-400 text-md" />
                            Joined {DateTime.fromJSDate(new Date(singer.joined_at)).toLocaleString(DateTime.DATE_MED)}
                        </div>
                    </>)}
                breadcrumbs={[
                    { name: 'Dashboard', url: route('dash')},
                    { name: 'Singers', url: route('singers.index')},
                    { name: singer.user.name, url: route('singers.show', singer) },
                ]}
                actions={[
                    { label: 'Edit Membership', icon: 'edit', url: route('singers.edit', singer)},
                    { label: 'Move', icon: 'chevron-down', url: '#'},
                    { label: 'Delete', icon: 'trash', onClick: () => setDeleteDialogIsOpen(true), variant: 'danger-outline'},
                ]}
            />
            <Dialog
                title="Delete Singer"
                content="Are you sure you want to deactivate this singer?
                All of their data will be permanently removed from our servers forever.
                This action cannot be undone."
                okLabel="Delete"
                okUrl={route('singers.destroy', singer)}
                isOpen={deleteDialogIsOpen}
                setIsOpen={setDeleteDialogIsOpen}
            />
            <div className="bg-gray-50">
                <div className="grid grid-cols-1 sm:grid-cols-4">

                    <div className="sm:col-span-3">
                        <div className="py-6 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 sm:border-b sm:border-b-gray-300">

                            <h2 className="text-xl leading-6 font-semibold text-gray-900 mb-4">Personal Details</h2>

                            <DetailList items={[
                                {
                                    label: 'Contact Details',
                                    value: <>
                                        <p>
                                            <i className="far fa-fw fa-envelope text-gray-500 mr-2" />{singer.user.email}
                                        </p>
                                        <p>
                                            <i className="far fa-fw fa-phone text-gray-500 mr-2" />{singer.user.phone ?? 'No phone'}
                                        </p>
                                    </>,
                                },
                                {
                                    label: 'Date of Birth',
                                    value: DateTime.fromJSDate(new Date(singer.user.dob)).toLocaleString(DateTime.DATE_MED) ?? 'No date of birth',
                                },
                                {
                                    label: 'Height',
                                    value: singer.user.height ? `${Math.round(singer.user.height)} cm` : 'Unknown',
                                },
                                {
                                    label: 'BHA Member ID',
                                    value: singer.user.bha_id ?? 'Unknown',
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
                                            <i className="far fa-fw fa-user text-gray-500 mr-2" />{singer.user.ice_name ?? 'No emergency contact'}
                                        </p>
                                        <p>
                                            <i className="far fa-fw fa-phone text-gray-500 mr-2" />{singer.user.ice_phone ?? 'No phone'}
                                        </p>
                                    </>,
                                },
                            ]}/>
                        </div>

                        <div className="py-6 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

                            <h2 className="text-xl leading-6 font-semibold text-gray-900 mb-4">Membership Details</h2>

                            <DetailList items={[
                                {
                                    label: 'Roles',
                                    value: <>
                                        {singer.roles.map(role => (
                                            <span className="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-200 text-gray-800 mr-1.5 mb-1.5">
                                            {role.name.split(' ')[0]}
                                        </span>
                                        ))}
                                    </>,
                                    colClass: 'sm:col-span-2',
                                },
                                {
                                    label: 'Reason for Joining',
                                    value: singer.reason_for_joining ?? 'Unknown',
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
                                        {DateTime.fromJSDate(new Date(singer.joined_at)).toLocaleString(DateTime.DATE_MED)}<br />
                                        <span className="text-sm text-gray-500 italic">
                                        Added {DateTime.fromJSDate(new Date(singer.created_at)).toLocaleString(DateTime.DATE_MED)}
                                    </span>
                                    </>,
                                },
                                {
                                    label: 'Last Login',
                                    value: DateTime.fromJSDate(new Date(singer.user.last_login)).toLocaleString(DateTime.DATE_MED),
                                },
                            ]} />
                        </div>
                    </div>

                    <div className="sm:col-span-1 sm:border-l sm:border-l-gray-300">
                        <div className="py-6 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 sm:border-b sm:border-b-gray-300">

                            <div className="-ml-2 -mt-2 flex flex-wrap items-baseline">
                                <h2 className="ml-2 mt-2 text-xl leading-6 font-semibold text-gray-900 mb-4">Onboarding</h2>
                                <p className="ml-2 mt-1 text-md text-gray-500 truncate">{singer.onboarding_enabled ? 'Enabled' : 'Disabled'}</p>
                            </div>

                            <div className="py-6 px-4 sm:px-3 lg:px-4">
                                <nav className="flex" aria-label="Progress">
                                    <ol role="list" className="space-y-6">
                                        {singer.tasks.map((task, index, tasks) => (
                                            <li key={index}>
                                        <span className="flex items-center">
                                            <span className="flex-shrink-0 h-5 w-5 relative flex items-center justify-center" aria-hidden="true">
                                                {task.pivot.completed
                                                && <i className="fas fa-fw fa-check-circle text-purple-600 text-sm" />
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
                                                <ButtonLink href={route(task.route, [singer.id, task.id])} size="xs" className="ml-3">Complete</ButtonLink>
                                            </>
                                            || <span className="ml-3 text-sm font-medium text-gray-500 group-hover:text-gray-900">{task.name}</span>
                                            }
                                        </span>
                                            </li>
                                        ))}
                                    </ol>
                                </nav>
                            </div>
                        </div>

                        <div className="py-6 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                            {singer.placement
                                ? <>
                                    <div className="pb-5 sm:flex sm:items-center sm:justify-between mb-4">
                                        <h2 className="text-xl leading-6 font-semibold text-gray-900">Voice Placement</h2>
                                        <div className="mt-3 sm:mt-0 sm:ml-4">
                                            <ButtonLink
                                                variant="primary"
                                                size="sm"
                                                href={route('singers.placements.edit', [singer.id, singer.placement.id])}
                                            >
                                                <i className="fas fa-fw fa-edit mr-2" />
                                                Edit
                                            </ButtonLink>
                                        </div>
                                    </div>

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
                                                minLabel={<i className="fas fa-fw fa-flute" />}
                                                maxLabel={<i className="fas fa-fw fa-trumpet fa-lg" />}
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
                                </>
                                :
                                <>
                                    <h2 className="text-xl leading-6 font-semibold text-gray-900 mb-4">Voice Placement</h2>

                                    <div className="text-center py-4 px-2">
                                        <i className="fal fa-fw fa-user-music text-gray-400 text-4xl mb-2" />
                                        <h3 className="mt-2 text-sm font-medium text-gray-900">No Voice Placement</h3>
                                        <p className="mt-1 text-sm text-gray-500">
                                            Get this singer started on their journey by creating their Voice Placement.
                                        </p>
                                        <div className="mt-6">
                                            <ButtonLink href={route('singers.placements.create', singer)} variant="primary">
                                                <i className="far fa-fw fa-plus mr-2" />
                                                Create Placement
                                            </ButtonLink>
                                        </div>
                                    </div>
                                </>
                            }
                        </div>
                    </div>

                </div>
            </div>
        </>
    );
}

Show.layout = page => <Layout children={page} title="Singers" />

export default Show;