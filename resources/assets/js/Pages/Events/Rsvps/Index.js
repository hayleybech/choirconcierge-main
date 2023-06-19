import React, {useState} from 'react';
import PageHeader from "../../../components/PageHeader";
import TenantLayout from "../../../Layouts/TenantLayout";
import Button from "../../../components/inputs/Button";
import AppHead from "../../../components/AppHead";
import AttendanceTag from "../../../components/Event/AttendanceTag";
import Icon from "../../../components/Icon";
import TextInput from "../../../components/inputs/TextInput";
import Label from "../../../components/inputs/Label";
import RsvpTag from "../../../components/Event/RsvpTag";

const Index = ({ event, voiceParts, singers }) => {
    const [absentReasons, setAbsentReasons] = useState({});

    return (
        <>
            <AppHead title={`RSVP Summary - ${event.title}`} />
            <PageHeader
                title="RSVP Summary"
                icon="calendar"
                breadcrumbs={[
                    { name: 'Dashboard', url: route('dash') },
                    { name: 'Events', url: route('events.index') },
                    { name: event.title, url: route('events.show', event) },
                    { name: 'RSVPs', url: route('events.rsvps.index', event) },
                ]}
            />

            <nav className="h-full overflow-y-auto" aria-label="Directory">
                {voiceParts.map((part) => (
                    <div key={part.id} className="relative">
                        <div className="z-10 sticky top-0 border-t border-b border-gray-200 bg-gray-50 px-6 py-1 text-sm font-medium text-gray-500">
                            <h3>{part.title}</h3>
                        </div>
                        <ul role="list" className="relative z-0 divide-y divide-gray-200">
                            {part.singers.map((singer) => (
                                <li key={singer.id} className="bg-white">
                                    <div className="relative px-6 py-5 flex flex-col sm:flex-row items-center space-y-3 sm:space-x-3 hover:bg-gray-50 justify-between items-stretch sm:items-center">
                                        <div className="flex space-x-2">
                                            <div className="shrink-0">
                                                <img className="h-12 w-12 rounded-lg" src={singer.user.avatar_url} alt={singer.user.name}/>
                                            </div>
                                            <div className="flex-1 min-w-0">
                                                <p className="text-sm font-medium text-gray-900">{singer.user.name}</p>
                                                <p className="text-sm">
                                                  <RsvpTag icon={singer.rsvp.icon} label={singer.rsvp.label} colour={singer.rsvp.colour} />
                                                </p>
                                            </div>
                                        </div>

                                    </div>
                                </li>
                            ))}
                        </ul>
                    </div>
                ))}
            </nav>
        </>
    );
}

Index.layout = page => <TenantLayout children={page} />

export default Index;