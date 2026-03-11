import React from 'react';
import PageHeader from "../../../components/PageHeader/PageHeader";
import TenantLayout from "../../../Layouts/TenantLayout";
import AppHead from "../../../components/AppHead";
import Icon from "../../../components/Icon";
import RsvpTag from "../../../components/Event/RsvpTag";
import Table, { TableCell } from "../../../components/Table";
import useRoute from "../../../hooks/useRoute";
import DateTag from '../../../components/DateTag';
import collect from 'collect.js';
import VoicePartTag from '../../../components/VoicePartTag';

const Index = ({ event, singers }) => {
  const { route } = useRoute();
  
  const headings = collect({
    singer: 'Singer',
    voice_part: 'Voice Part',
    rsvp: 'RSVP',
    dietary: 'Dietary / Medical',
  });

  const counts = [
    { label: 'Going', textColour: 'text-emerald-500', icon: 'check', count: singers.filter(singer => singer.membership.rsvp.response === 'yes').length },
    { label: 'Maybe', textColour: 'text-amber-500', icon: 'question', count: singers.filter(singer => singer.membership.rsvp.response === 'maybe').length },
    { label: 'No RSVP', textColour: 'text-red-500', icon: 'question', count: singers.filter(singer => singer.membership.rsvp.response === 'unknown').length },
    { label: 'Not going', textColour: 'text-gray-500', icon: 'times', count: singers.filter(singer => singer.membership.rsvp.response === 'no').length },
  ];

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

      <div className="bg-white py-4 border-b border-gray-200 flex justify-around mb-6 shadow rounded-lg overflow-hidden divide-x divide-gray-100">
        {counts.map(({ label, textColour, icon, count }) => (
          <div className="text-center flex flex-col items-center justify-center py-2 flex-1" key={label}>
            <div className={`flex items-center gap-2 font-bold ${textColour} mb-1`}>
                <Icon icon={icon} />
                {label}
            </div>
            <span className="text-2xl font-bold text-gray-900">{count}</span>
          </div>
        ))}
      </div>

      <Table
        headings={headings}
        body={singers.map((singer) => (
          <tr key={singer.id}>
            <TableCell>
              <div className="flex items-center space-x-3">
                <div className="shrink-0">
                  <img className="h-8 w-8 rounded-md object-cover" src={singer.membership.user.avatar_url} alt={singer.membership.user.name}/>
                </div>
                <div className="text-sm font-medium text-gray-900">{singer.membership.user.name}</div>
              </div>
            </TableCell>
            <TableCell>
				<VoicePartTag colour={singer.voice_part.colour} title={singer.voice_part.title} />
            </TableCell>
            <TableCell>
              <div className="flex flex-col">
                <RsvpTag icon={singer.membership.rsvp.icon} label={singer.membership.rsvp.label} colour={singer.membership.rsvp.colour} />
                {!!singer.membership.rsvp.updated_at && (
                  <DateTag icon="pencil" label="Updated" date={singer.membership.rsvp.updated_at} format="DATETIME_SHORT" className="text-gray-400 mt-1" />
                )}
              </div>
            </TableCell>
            <TableCell>
               <div className="text-xs space-y-0.5">
                  {singer.membership.user.dietary_requirements ? (
                      <div>
                        <span className="font-semibold text-amber-600">Dietary: </span>
                        <span className="text-amber-800">{singer.membership.user.dietary_requirements}</span>
                      </div>
                  ) : (
                      <div className="text-gray-400">
                        <span className="font-semibold">Dietary: </span>
                        None
                      </div>
                  )}
                  {singer.membership.user.medical_conditions ? (
                      <div>
                        <span className="font-semibold text-amber-600">Medical: </span>
                        <span className="text-amber-800">{singer.membership.user.medical_conditions}</span>
                      </div>
                  ) : (
                      <div className="text-gray-400">
                        <span className="font-semibold">Medical: </span>
                        None
                      </div>
                  )}
               </div>
            </TableCell>
          </tr>
        ))}
      />
    </>
  );
}

Index.layout = page => <TenantLayout children={page} />

export default Index;