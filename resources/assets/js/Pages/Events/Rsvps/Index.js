import React from 'react';
import PageHeader from "../../../components/PageHeader";
import TenantLayout from "../../../Layouts/TenantLayout";
import AppHead from "../../../components/AppHead";
import Icon from "../../../components/Icon";
import RsvpTag from "../../../components/Event/RsvpTag";
import CollapseGroup from "../../../components/CollapseGroup";
import useRoute from "../../../hooks/useRoute";
import DateTag from '../../../components/DateTag';

const Index = ({ event, voiceParts }) => {
  const { route } = useRoute();
  
  return (
    <>
      <AppHead title={`RSVP List - ${event.title}`} />
      <PageHeader
        title="RSVP List"
        icon="calendar"
        breadcrumbs={[
          { name: 'Dashboard', url: route('dash') },
          { name: 'Events', url: route('events.index') },
          { name: event.title, url: route('events.show', {event}) },
          { name: 'RSVP List', url: route('events.rsvps.index', {event}) },
        ]}
      />

      <CollapseGroup items={voiceParts.map((part) => ({
        title: part.title,
        show: true,
        defaultOpen: true,
        content: (
          <div key={part.id} className="relative">
            <div className="flex bg-white py-4 border-b border-gray-200">
              {[
                { label: 'Going', colour: 'emerald', icon: 'check', count: part.singers.filter(singer => singer.membership.rsvp.response === 'yes').length },
                { label: 'Unknown', colour: 'red', icon: 'question', count: part.singers.filter(singer => singer.membership.rsvp.response === 'unknown').length },
                { label: 'Not going', colour: 'gray', icon: 'times', count: part.singers.filter(singer => singer.membership.rsvp.response === 'no').length },
              ].map(({ label, colour, icon, count}) => (
                <div className="w-1/3 text-center flex flex-col items-center justify-between" key={label}>
                  <div className="hidden md:block">
                    <RsvpTag icon={icon} label={label} colour={colour} size="md" className="font-bold block" />
                  </div>
                  <div className={`flex flex-col items-center md:hidden font-bold text-${colour}-500`}>
                    <Icon icon={icon} className="text-lg" />
                    {label}
                  </div>
                  {count}
                </div>
              ))}
            </div>
            <ul role="list" className="relative z-0 divide-y divide-gray-200">
              {part.singers.map((singer) => (
                <li key={singer.id} className="bg-white">
                  <div className="relative px-6 py-5 flex flex-col sm:flex-row items-center space-y-3 sm:space-x-3 hover:bg-gray-50 justify-between items-stretch sm:items-center">
                    <div className="flex space-x-2">
                      <div className="shrink-0">
                        <img className="h-12 w-12 rounded-lg" src={singer.membership.user.avatar_url} alt={singer.membership.user.name}/>
                      </div>

                      <div className="flex-1 min-w-0">
                        <p className="text-sm font-medium text-gray-900">{singer.membership.user.name}</p>
                        <div className="text-sm flex gap-4">
                          <RsvpTag icon={singer.membership.rsvp.icon} label={singer.membership.rsvp.label} colour={singer.membership.rsvp.colour} className="shrink-0" />
                            {!!singer.membership.rsvp.updated_at && (
                                <DateTag label="Updated" date={singer.membership.rsvp.updated_at} format="DATETIME_SHORT" className="text-gray-400" />
                            )}
                        </div>
                      </div>
                    </div>

                      <div className="flex space-between gap-4">
                        {singer.membership.user.dietary_requirements ? (
                        <div>
                            <strong className="text-amber-600 mb-2">Dietary Requirements</strong>
                            <p className="text-amber-800 text-xs">{singer.membership.user.dietary_requirements}</p>
                        </div>
                        ) : (
                        <div>
                            <strong className="text-gray-500 mb-2">Dietary Requirements</strong>
                            <p className="text-gray-700 text-xs">None listed</p>
                        </div>
                        )}
                        {singer.membership.user.medical_conditions ? (
                          <div>
                              <strong className="text-amber-600 mb-2">Medical Conditions</strong>
                              <p className="text-amber-800 text-xs">{singer.membership.user.medical_conditions}</p>
                          </div>
                        ) : (
                          <div>
                              <strong className="text-gray-500 mb-2">Medical Conditions</strong>
                              <p className="text-gray-700 text-xs">None listed</p>
                          </div>
                        )}
                      </div>

                  </div>
                </li>
              ))}
            </ul>
          </div>
        ),
      }))} />
    </>
  );
}

Index.layout = page => <TenantLayout children={page} />

export default Index;