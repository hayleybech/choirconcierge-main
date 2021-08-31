import React from 'react'
import Layout from "../../Layouts/Layout";
import SingerPageHeader from "./SingerPageHeader";
import VoicePartTag from "../../components/VoicePartTag";
import SingerCategoryTag from "../../components/SingerCategoryTag";
import ButtonLink from "../../components/inputs/ButtonLink";
import {DateTime} from "luxon";

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

const Show = ({singer}) => (
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
                { label: 'Delete', icon: 'times', url: '#', variant: 'danger'},
            ]}
        />
        <div className="bg-gray-50">
            <div className="grid grid-cols-1 sm:grid-cols-4">

                <div className="sm:col-span-3">
                    <div className="py-6 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 sm:border-b sm:border-b-gray-300">

                        <h2 className="text-xl leading-6 font-semibold text-gray-900 mb-4">Personal Details</h2>

                        <dl className="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2 md:grid-cols-4">
                            <div className="sm:col-span-1">
                                <dt className="text-sm font-medium text-gray-500">
                                    Contact Details
                                </dt>
                                <dd className="mt-1 text-sm text-gray-900">
                                    <p>
                                        <i className="far fa-fw fa-envelope text-gray-500 mr-2" />{singer.user.email}
                                    </p>
                                    <p>
                                        <i className="far fa-fw fa-phone text-gray-500 mr-2" />{singer.user.phone ?? 'No phone'}
                                    </p>
                                </dd>
                            </div>

                            <div className="sm:col-span-1">
                                <dt className="text-sm font-medium text-gray-500">
                                    Date of Birth
                                </dt>
                                <dd className="mt-1 text-sm text-gray-900">
                                    {DateTime.fromJSDate(new Date(singer.user.dob)).toLocaleString(DateTime.DATE_MED) ?? 'No date of birth'}
                                </dd>
                            </div>

                            <div className="sm:col-span-1">
                                <dt className="text-sm font-medium text-gray-500">
                                    Height
                                </dt>
                                <dd className="mt-1 text-sm text-gray-900">
                                    {singer.user.height ? `${Math.round(singer.user.height)} cm` : 'Unknown'}
                                </dd>
                            </div>

                            <div className="sm:col-span-1">
                                <dt className="text-sm font-medium text-gray-500">
                                    BHA Member ID
                                </dt>
                                <dd className="mt-1 text-sm text-gray-900">
                                    {singer.user.bha_id ?? 'Unknown'}
                                </dd>
                            </div>

                            <div className="sm:col-span-1">
                                <dt className="text-sm font-medium text-gray-500">
                                    Address
                                </dt>
                                <dd className="mt-1 text-sm text-gray-900">
                                    {singer.user.address_street_1
                                        ? (<>
                                            {singer.user.address_street_1}<br />
                                            {singer.user.address_street_2 && (<>{singer.user.address_street_2}<br /></>)}
                                            {`${singer.user.address_suburb}, ${singer.user.address_state} ${singer.user.address_postcode}`}
                                        </>)
                                        : 'No address'
                                    }
                                </dd>
                            </div>

                            <div className="sm:col-span-1">
                                <dt className="text-sm font-medium text-gray-500">
                                    Profession
                                </dt>
                                <dd className="mt-1 text-sm text-gray-900">
                                    {singer.user.profession ?? 'None listed'}
                                </dd>
                            </div>

                            <div className="sm:col-span-1">
                                <dt className="text-sm font-medium text-gray-500">
                                    Other Skills
                                </dt>
                                <dd className="mt-1 text-sm text-gray-900">
                                    {singer.user.skills ?? 'None listed'}
                                </dd>
                            </div>

                            <div className="sm:col-span-1">
                                <dt className="text-sm font-medium text-gray-500">
                                    Emergency Contact
                                </dt>
                                <dd className="mt-1 text-sm text-gray-900">
                                    <p>
                                        <i className="far fa-fw fa-user text-gray-500 mr-2" />{singer.user.ice_name ?? 'No emergency contact'}
                                    </p>
                                    <p>
                                        <i className="far fa-fw fa-phone text-gray-500 mr-2" />{singer.user.ice_phone ?? 'No phone'}
                                    </p>
                                </dd>
                            </div>

                        </dl>
                    </div>

                    <div className="py-6 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

                        <h2 className="text-xl leading-6 font-semibold text-gray-900 mb-4">Membership Details</h2>

                        <dl className="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2 md:grid-cols-4">
                            <div className="sm:col-span-2">
                                <dt className="text-sm font-medium text-gray-500">
                                    Roles
                                </dt>
                                <dd className="mt-1 text-sm text-gray-900">
                                    {singer.roles.map(role => (
                                        <span className="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-200 text-gray-800 mr-1.5 mb-1.5">
                                            {role.name.split(' ')[0]}
                                        </span>
                                    ))}
                                </dd>
                            </div>

                            <div className="sm:col-span-1">
                                <dt className="text-sm font-medium text-gray-500">
                                    Reason for joining
                                </dt>
                                <dd className="mt-1 text-sm text-gray-900">
                                    {singer.reason_for_joining ?? 'Unknown'}
                                </dd>
                            </div>

                            <div className="sm:col-span-1">
                                <dt className="text-sm font-medium text-gray-500">
                                    Referred by
                                </dt>
                                <dd className="mt-1 text-sm text-gray-900">
                                    {singer.referrer ?? 'Unknown'}
                                </dd>
                            </div>

                            <div className="sm:col-span-2">
                                <dt className="text-sm font-medium text-gray-500">
                                    Notes / Membership Details
                                </dt>
                                <dd className="mt-1 text-sm text-gray-900">
                                    {singer.membership_details ?? 'N/A'}
                                </dd>
                            </div>

                            <div className="sm:col-span-1">
                                <dt className="text-sm font-medium text-gray-500">
                                    Member Since
                                </dt>
                                <dd className="mt-1 text-sm text-gray-900">
                                    {DateTime.fromJSDate(new Date(singer.joined_at)).toLocaleString(DateTime.DATE_MED)}<br />
                                    <span className="text-sm text-gray-500 italic">
                                        Added {DateTime.fromJSDate(new Date(singer.created_at)).toLocaleString(DateTime.DATE_MED)}
                                    </span>
                                </dd>
                            </div>

                            <div className="sm:col-span-1">
                                <dt className="text-sm font-medium text-gray-500">
                                    Last Login
                                </dt>
                                <dd className="mt-1 text-sm text-gray-900">
                                    {DateTime.fromJSDate(new Date(singer.user.last_login)).toLocaleString(DateTime.DATE_MED)}
                                </dd>
                            </div>

                        </dl>
                    </div>
                </div>

                <div className="sm:col-span-1 sm:border-l sm:border-l-gray-300">
                    <div className="py-6 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 sm:border-b sm:border-b-gray-300">

                        <div className="-ml-2 -mt-2 flex flex-wrap items-baseline">
                            <h2 className="ml-2 mt-2 text-xl leading-6 font-semibold text-gray-900 mb-4">Onboarding</h2>
                            <p className="ml-2 mt-1 text-md text-gray-500 truncate">{singer.onboarding_enabled ? 'Enabled' : 'Disabled'}</p>
                        </div>
                    </div>

                    <div className="py-6 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                        {singer.placement 
                            ? <>
                                <h2 className="text-xl leading-6 font-semibold text-gray-900 mb-4">Voice Placement</h2>
        
                                <dl className="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2">
                                    <div className="sm:col-span-1">
                                        <dt className="text-sm font-medium text-gray-500">
                                            Voice Part
                                        </dt>
                                        <dd className="mt-1 text-sm text-gray-900">
                                            <VoicePartTag title={singer.voice_part.title} colour={singer.voice_part.colour} />
                                        </dd>
                                    </div>

                                    <div className="sm:col-span-1">
                                        <dt className="text-sm font-medium text-gray-500">
                                            Voice Tone
                                        </dt>
                                        <dd className="mt-1 text-sm text-gray-900">
                                            <Range
                                                min={1}
                                                max={3}
                                                minLabel={<i className="fas fa-fw fa-flute" />}
                                                maxLabel={<i className="fas fa-fw fa-trumpet fa-lg" />}
                                                value={singer.placement.voice_tone}
                                            />
                                        </dd>
                                    </div>

                                    <div className="sm:col-span-1">
                                        <dt className="text-sm font-medium text-gray-500">
                                            Pitch Skill
                                        </dt>
                                        <dd className="mt-1 text-sm text-gray-900">
                                            <Progress value={singer.placement.skill_pitch} min={1} max={5} />
                                        </dd>
                                    </div>

                                    <div className="sm:col-span-1">
                                        <dt className="text-sm font-medium text-gray-500">
                                            Harmony Skill
                                        </dt>
                                        <dd className="mt-1 text-sm text-gray-900">
                                            <Progress value={singer.placement.skill_harmony} min={1} max={5} />
                                        </dd>
                                    </div>

                                    <div className="sm:col-span-1">
                                        <dt className="text-sm font-medium text-gray-500">
                                            Performance Skill
                                        </dt>
                                        <dd className="mt-1 text-sm text-gray-900">
                                            <Progress value={singer.placement.skill_performance} min={1} max={5} />
                                        </dd>
                                    </div>

                                    <div className="sm:col-span-1">
                                        <dt className="text-sm font-medium text-gray-500">
                                            Sight Reading Skill
                                        </dt>
                                        <dd className="mt-1 text-sm text-gray-900">
                                            <Progress value={singer.placement.skill_sightreading} min={1} max={5} />
                                        </dd>
                                    </div>

                                    <div className="sm:col-span-2">
                                        <dt className="text-sm font-medium text-gray-500">
                                            Experience
                                        </dt>
                                        <dd className="mt-1 text-sm text-gray-900">
                                            {singer.placement.experience ?? 'None listed'}
                                        </dd>
                                    </div>

                                    <div className="sm:col-span-2">
                                        <dt className="text-sm font-medium text-gray-500">
                                            Instruments
                                        </dt>
                                        <dd className="mt-1 text-sm text-gray-900">
                                            {singer.placement.instruments ?? 'None listed'}
                                        </dd>
                                    </div>
                                </dl>
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

Show.layout = page => <Layout children={page} title="Singers" />

export default Show;