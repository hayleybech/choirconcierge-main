import React from 'react';
import PageHeader from '../../../components/PageHeader/PageHeader';
import TenantLayout from '../../../Layouts/TenantLayout';
import AppHead from '../../../components/AppHead';
import Icon from '../../../components/Icon';
import RsvpTag from '../../../components/Event/RsvpTag';
import Table, { TableCell } from '../../../components/Table';
import useRoute from '../../../hooks/useRoute';
import DateTag from '../../../components/DateTag';
import collect from 'collect.js';
import VoicePartTag from '../../../components/VoicePartTag';
import Badge from '../../../components/Badge';
import IndexContainer from '../../../components/IndexContainer';
import RsvpTableMobile from './RsvpTableMobile';
import Pagination from '../../../components/Pagination';
import useFilterPane from '../../../hooks/useFilterPane';
import useSortFilterForm from '../../../hooks/useSortFilterForm';
import FilterSortPane from '../../../components/FilterSortPane';
import Sorts from '../../../components/Sorts';
import RsvpFilters from '../../../components/RsvpFilters';
import TableHeadingSort from '../../../components/TableHeadingSort';
import { Link } from '@inertiajs/react';

const Index = ({ event, allSingers, pagination, totalEnsemblesCount, voiceParts, ensembles, counts }) => {
	const { route } = useRoute();
	const [showFilters, setShowFilters, filterAction, hasNonDefaultFilters] = useFilterPane();

	const showEnsemble = event.ensembles.length > 0 || totalEnsemblesCount > 1;

	const sorts = [
		{ id: 'full-name', name: 'Name', default: true },
		{ id: 'rsvp-response', name: 'RSVP Response' },
		{ id: 'rsvp-updated', name: 'Updated' },
	];

	const filters = [
		{ name: 'user.name', defaultValue: '' },
		{ name: 'enrolments.voice_part_id', multiple: true },
		{ name: 'enrolments.ensemble_id', multiple: true },
		{ name: 'rsvp.response', multiple: true },
	];

	const sortFilterForm = useSortFilterForm(['events.rsvps.index', { event: event.id }], filters, sorts);

	const headings = collect({
		singer: (
			<TableHeadingSort form={sortFilterForm} sort="full-name">
				Name
			</TableHeadingSort>
		),
		voice_part: 'Voice Part',
		rsvp: (
			<TableHeadingSort form={sortFilterForm} sort="rsvp-response">
				RSVP
			</TableHeadingSort>
		),
		updated: (
			<TableHeadingSort form={sortFilterForm} sort="rsvp-updated">
				Updated
			</TableHeadingSort>
		),
		dietary: 'Dietary / Medical',
	});

	const countsData = [
		{ label: 'Going', textColour: 'text-emerald-500', icon: 'check', count: counts.yes },
		// { label: 'Maybe', textColour: 'text-amber-500', icon: 'question', count: counts.maybe },
		{ label: 'No RSVP', textColour: 'text-red-500', icon: 'question', count: counts.unknown },
		{ label: 'Not going', textColour: 'text-gray-500', icon: 'times', count: counts.no },
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
				actions={[filterAction]}
				optionsVariant={hasNonDefaultFilters ? 'success-solid' : 'secondary'}
			/>

			<div className="bg-white border-b border-gray-200 grid grid-cols-3">
				{countsData.map(({ label, textColour, icon, count }) => (
					<div
						className="text-center flex flex-col items-center justify-center py-2 lg:py-4 flex-1"
						key={label}
					>
						<div className={`flex items-center gap-2 font-bold ${textColour} mb-1 text-sm md:text-base`}>
							<Icon icon={icon} />
							{label}
						</div>
						<span className="text-xl md:text-2xl font-bold text-gray-900">{count}</span>
					</div>
				))}
			</div>

			<IndexContainer
				showFilters={showFilters}
				filterPane={
					<FilterSortPane
						sorts={<Sorts sorts={sorts} form={sortFilterForm} />}
						filters={
							<RsvpFilters
								event={event}
								voiceParts={voiceParts}
								ensembles={ensembles}
								form={sortFilterForm}
							/>
						}
						closeFn={() => setShowFilters(false)}
					/>
				}
				tableMobile={
					<RsvpTableMobile singers={allSingers} pagination={pagination} showEnsemble={showEnsemble} />
				}
				tableDesktop={
					<Table
						headings={headings}
						pagination={<Pagination details={pagination} />}
						body={allSingers.map(singer => (
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
										<Link
											href={route('singers.show', { singer })}
											className="text-sm font-medium text-purple-600 hover:text-purple-700 focus:text-purple-700 hover:underline focus:underline"
										>
											{singer.user.name}
										</Link>
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
									<RsvpTag
										icon={singer.rsvp.icon}
										label={singer.rsvp.label}
										colour={singer.rsvp.colour}
									/>
								</TableCell>
								<TableCell>
									{!!singer.rsvp.updated_at && (
										<DateTag
											icon="pencil"
											label="Updated"
											date={singer.rsvp.updated_at}
											format="DATETIME_SHORT"
											className="text-gray-400"
										/>
									)}
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
};

Index.layout = page => <TenantLayout children={page} />;

export default Index;
