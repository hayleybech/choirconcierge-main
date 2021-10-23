import React from 'react';
import {Tab} from "@headlessui/react";
import classNames from "../../classNames";
import VoicePartTag from "../VoicePartTag";
import ButtonLink from "../inputs/ButtonLink";
import SectionTitle from "../SectionTitle";
import Icon from "../Icon";
import SectionHeader from "../SectionHeader";

const RsvpSummary = ({ rsvpCount, voicePartsRsvpCount }) => (
    <div className="py-6">

        <div className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <SectionHeader>
                <SectionTitle>Rsvp Summary</SectionTitle>
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
                            { label: 'Going', colour: 'green-500', icon: 'check', count: rsvpCount.yes },
                            { label: 'Unknown', colour: 'yellow-500', icon: 'question', count: rsvpCount.unknown },
                            { label: 'Not going', colour: 'red-500', icon: 'times', count: rsvpCount.no },
                        ].map(({ label, colour, icon, count}) => (
                            <div className="w-1/3 text-center" key={label}>
                                <Icon icon={icon} className={`text-${colour}`} />
                                <p className={`font-semibold text-${colour}`}>{label}</p>
                                {count}
                            </div>
                        ))}
                    </div>
                </Tab.Panel>
                <Tab.Panel className="py-6 px-4">
                    <p className="text-green-500 font-semibold mb-4">
                        <Icon icon="check" mr />
                        Going
                    </p>
                    <div className="flex">
                        {voicePartsRsvpCount.yes.map(voice_part => (
                            <div className="w-1/2 text-center" key={voice_part.id}>
                                <p className="mb-2">
                                    <VoicePartTag title={voice_part.title} colour={voice_part.colour} />
                                </p>
                                {voice_part.singers_going_count} / {voice_part.singers_count}
                            </div>
                        ))}
                    </div>
                </Tab.Panel>
            </Tab.Panels>
        </Tab.Group>

    </div>
);

export default RsvpSummary;