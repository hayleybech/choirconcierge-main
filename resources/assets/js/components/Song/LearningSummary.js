import React from 'react';
import {Tab} from "@headlessui/react";
import classNames from "../../classNames";
import VoicePartTag from "../VoicePartTag";
import ButtonLink from "../inputs/ButtonLink";
import SectionTitle from "../SectionTitle";
import Icon from "../Icon";
import SectionHeader from "../SectionHeader";
import LearningStatus from "../../LearningStatus";
import LearningStatusTag from "./LearningStatusTag";

const LearningSummary = ({ status_count, voice_parts_count, song }) => (
    <div className="py-6">

        <div className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <SectionHeader>
                <SectionTitle>Learning Summary</SectionTitle>

                <ButtonLink
                    variant="primary"
                    size="sm"
                    href={route('songs.singers.index', song)}
                >
                    <Icon icon="edit" />
                    Edit
                </ButtonLink>
            </SectionHeader>
        </div>


        <Tab.Group>
            <div className="border-b border-gray-200">
                <div className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                    <Tab.List className="-mb-px flex">
                        {['By Status', 'By Voice Part'].map(label => (
                            <Tab key={label} className={({ selected }) => classNames(
                                selected
                                    ? 'border-purple-500 text-purple-600'
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                                'w-1/2 py-2 px-1 text-center border-b-2 font-medium text-sm'
                            )}>
                                {label}
                            </Tab>
                        ))}
                    </Tab.List>
                </div>
            </div>
            <Tab.Panels>
                <Tab.Panel className="py-6 px-4">
                    <div className="flex">
                        {[
                            { status: new LearningStatus('performance-ready'), count: status_count.performance_ready },
                            { status: new LearningStatus('assessment-ready'), count: status_count.assessment_ready },
                            { status: new LearningStatus('not-started'), count: status_count.learning },
                        ].map(({ status, count }) => (
                            <div className="w-1/3 text-center" key={status.slug}>
                                <Icon icon={status.icon} className={status.textColour} />
                                <p className={`font-semibold ${status.textColour}`}>{status.shortTitle}</p>
                                {count}
                            </div>
                        ))}
                    </div>
                </Tab.Panel>
                <Tab.Panel className="py-6 px-4">
                    <LearningStatusTag status={new LearningStatus('performance-ready')} />
                    <div className="flex mt-4">
                        {voice_parts_count.performance_ready.map(voice_part => (
                            <div className="w-1/2 text-center" key={voice_part.id}>
                                <p className="mb-2">
                                    <VoicePartTag title={voice_part.title} colour={voice_part.colour} />
                                </p>
                                {voice_part.performance_ready_count} / {voice_part.singers_count}
                            </div>
                        ))}
                    </div>
                </Tab.Panel>
            </Tab.Panels>
        </Tab.Group>

    </div>
);

export default LearningSummary;