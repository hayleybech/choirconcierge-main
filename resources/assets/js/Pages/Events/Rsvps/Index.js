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
import Badge from '../../../components/Badge';
import IndexContainer from '../../../components/IndexContainer';

const Index = ({ event, singers, totalEnsemblesCount }) => {
  const { route } = useRoute();
  
  const showEnsemble = event.ensembles.length > 0 || totalEnsemblesCount > 1;
  
  const headings = collect({
    singer: 'Singer',
    voice_part: 'Voice Part',
    rsvp: 'RSVP',
    dietary: 'Dietary / Medical',
  });

  const counts = [
    { label: 'Going', textColour: 'text-emerald-500', icon: 'check', count: singers.filter(singer => singer.rsvp.response === 'yes').length },
    { label: 'Maybe', textColour: 'text-amber-500', icon: 'question', count: singers.filter(singer => singer.rsvp.response === 'maybe').length },
    { label: 'No RSVP', textColour: 'text-red-500', icon: 'question', count: singers.filter(singer => singer.rsvp.response === 'unknown').length },
    { label: 'Not going', textColour: 'text-gray-500', icon: 'times', count: singers.filter(singer => singer.rsvp.response === 'no').length },
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
					{ name: event.title, url: route('events.show', { event }) },
					{ name: 'RSVP List', url: route('events.rsvps.index', { event }) },
				]}
			/>

			<div className="bg-white py-4 border-b border-gray-200 flex justify-around overflow-hidden divide-x divide-gray-100">
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

			<IndexContainer
				tableDesktop={
					<Table
						headings={headings}
						body={singers.map(singer => (
							<tr key={singer.id}>
								<TableCell>
									<div className="flex items-center space-x-3">
										<div className="shrink-0">
											<img
												className="h-8 w-8 rounded-md object-cover"
												src={singer.user.avatar_url}
												alt={singer.user.name}
											/>
										</div>
										<div className="text-sm font-medium text-gray-900">{singer.user.name}</div>
									</div>
								</TableCell>
								<TableCell>
									<ul className="flex flex-col gap-1.5">
										{singer.enrolments.map(enrolment => (
											<li key={enrolment.id} className="flex gap-1 items-center">
												{showEnsemble && (
													<Badge colour="bg-purple-100 text-purple-800">
														{enrolment.ensemble.name}
													</Badge>
												)}
												{enrolment.voice_part && (
													<VoicePartTag
														title={enrolment.voice_part.title}
														colour={enrolment.voice_part.colour}
													/>
												)}
											</li>
										))}
									</ul>
								</TableCell>
								<TableCell>
									<div className="flex flex-col">
										<RsvpTag
											icon={singer.rsvp.icon}
											label={singer.rsvp.label}
											colour={singer.rsvp.colour}
										/>
										{!!singer.rsvp.updated_at && (
											<DateTag
												icon="pencil"
												label="Updated"
												date={singer.rsvp.updated_at}
												format="DATETIME_SHORT"
												className="text-gray-400 mt-1"
											/>
										)}
									</div>
								</TableCell>
								<TableCell>
									<div className="text-xs space-y-0.5">
										{singer.user.dietary_requirements ? (
											<div>
												<span className="font-semibold text-amber-600">Dietary: </span>
												<span className="text-amber-800">
													{singer.user.dietary_requirements}
												</span>
											</div>
										) : (
											<div className="text-gray-400">
												<span className="font-semibold">Dietary: </span>
												None
											</div>
										)}
										{singer.user.medical_conditions ? (
											<div>
												<span className="font-semibold text-amber-600">Medical: </span>
												<span className="text-amber-800">{singer.user.medical_conditions}</span>
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
				}
			/>
		</>
  );
}

Index.layout = page => <TenantLayout children={page} />

export default Index;