import React, { useState, Fragment } from 'react';
import { Menu, Transition } from '@headlessui/react';
import PageHeader from '../../../components/PageHeader/PageHeader';
import TenantLayout from '../../../Layouts/TenantLayout';
import AppHead from '../../../components/AppHead';
import Icon from '../../../components/Icon';
import RsvpTag from '../../../components/Event/RsvpTag';
import Table, { TableCell, THead, TBody, TableHeading } from '../../../components/Table';
import useRoute from '../../../hooks/useRoute';
import DateTag from '../../../components/DateTag';
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
import SingerStatus from '../../../SingerStatus';
import SingerCategoryTag from '../../../components/SingerCategoryTag';
import { handleNameSort } from '../../../utils/sortHelpers';
import Button from '../../../components/inputs/Button';
import CheckboxInput from '../../../components/inputs/CheckboxInput';
import classNames from '../../../classNames';

const Index = ({
	event,
	allSingers,
	pagination,
	totalEnsemblesCount,
	voiceParts,
	ensembles,
	singerCategories,
	counts,
	customFields,
}) => {
	const { route } = useRoute();
	const [showFilters, setShowFilters, filterAction, hasNonDefaultFilters] = useFilterPane();

	const defaultColumns = ['singer', 'voice_part', 'rsvp', 'updated', 'dietary'];
	const [visibleColumns, setVisibleColumns] = useState(() => {
		const saved = localStorage.getItem('rsvp-columns');
		return saved ? JSON.parse(saved) : defaultColumns;
	});

	const toggleColumn = columnId => {
		const newVisibleColumns = visibleColumns.includes(columnId)
			? visibleColumns.filter(c => c !== columnId)
			: [...visibleColumns, columnId];
		setVisibleColumns(newVisibleColumns);
		localStorage.setItem('rsvp-columns', JSON.stringify(newVisibleColumns));
	};

	const columnsAction = (
		<Menu as="div" className="relative inline-block text-left ml-3">
			<Menu.Button as={Button} variant="secondary" size="sm">
				<Icon icon="columns" mr />
				Columns
			</Menu.Button>
			<Transition
				as={Fragment}
				enter="transition ease-out duration-100"
				enterFrom="transform opacity-0 scale-95"
				enterTo="transform opacity-100 scale-100"
				leave="transition ease-in duration-75"
				leaveFrom="transform opacity-100 scale-100"
				leaveTo="transform opacity-0 scale-95"
			>
				<Menu.Items className="absolute right-0 mt-2 w-56 origin-top-right divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none z-30">
					<Menu.Item>
						{({ active }) => (
							<button
								onClick={() => {
									setVisibleColumns(defaultColumns);
									localStorage.setItem('rsvp-columns', JSON.stringify(defaultColumns));
								}}
								className={classNames(
									'group flex w-full items-center justify-center px-2 py-2 text-xs text-gray-600',
									active ? 'text-gray-600' : 'text-gray-700',
								)}
							>
								<Icon icon="undo" mr className="text-gray-400 text-xs" />
								<span className="underline group-hover:no-underline">Reset</span>
							</button>
						)}
					</Menu.Item>
					<div className="px-1 py-1 ">
						{[
							{ id: 'voice_part', label: 'Voice Part' },
							{ id: 'rsvp', label: 'RSVP' },
							{ id: 'updated', label: 'Updated' },
							{ id: 'dietary', label: 'Dietary / Medical' },
						].map(col => (
							<Menu.Item key={col.id}>
								{({ active }) => (
									<button
										onClick={() => toggleColumn(col.id)}
										className={classNames(
											'group flex w-full items-center px-2 py-2 text-sm',
											!col.disabled && active && 'bg-purple-200 text-gray-600',
											col.disabled && 'text-gray-400 ',
											!col.disabled && !active && 'text-gray-700'
										)}
										disabled={col.disabled}
									>
										<CheckboxInput
											checked={visibleColumns.includes(col.id)}
											readOnly
											disabled={col.disabled}
											className="mr-2"
										/>
										{col.label}
									</button>
								)}
							</Menu.Item>
						))}
					</div>
					{customFields.length > 0 && (
						<div className="px-1 py-1">
							<div className="px-2 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
								Custom Fields
							</div>
							{customFields.map(cf => (
								<Menu.Item key={`cf-${cf.id}`}>
									{({ active }) => (
										<button
											onClick={() => toggleColumn(`cf-${cf.id}`)}
											className={`${
												active ? 'bg-purple-200 text-gray-600' : 'text-gray-700'
											} group flex w-full items-center px-2 py-2 text-sm text-left`}
										>
											<CheckboxInput
												checked={visibleColumns.includes(`cf-${cf.id}`)}
												readOnly
												className="mr-2"
											/>
											{cf.name}
										</button>
									)}
								</Menu.Item>
							))}
						</div>
					)}
				</Menu.Items>
			</Transition>
		</Menu>
	);

	const showEnsemble = event.ensembles.length > 0 || totalEnsemblesCount > 1;

	const sorts = [
		{ id: 'full-name', name: 'First Name', default: true },
		{ id: 'last-name-first', name: 'Last Name' },
		{ id: 'rsvp-response', name: 'RSVP Response' },
		{ id: 'rsvp-updated', name: 'Updated' },
		{ id: 'dietary-medical', name: 'Dietary / Medical' },
	];

	const filters = [
		{ name: 'user.name', defaultValue: '' },
		{ name: 'enrolments.voice_part_id', multiple: true },
		{ name: 'enrolments.ensemble_id', multiple: true },
		{ name: 'rsvp.response', multiple: true },
		{
			name: 'category.id',
			multiple: true,
			defaultValue: singerCategories.find(c => c.name === 'Members')?.id
				? [singerCategories.find(c => c.name === 'Members').id]
				: [],
		},
	];

	const sortFilterForm = useSortFilterForm(['events.rsvps.index', { event: event.id }], filters, sorts);

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
				actions={[filterAction, columnsAction]}
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
								singerCategories={singerCategories}
							/>
						}
						closeFn={() => setShowFilters(false)}
					/>
				}
				tableMobile={
					<RsvpTableMobile
						singers={allSingers}
						pagination={pagination}
						showEnsemble={showEnsemble}
						visibleColumns={visibleColumns}
						customFields={customFields}
					/>
				}
				tableDesktop={
					<Table pagination={<Pagination details={pagination} />}>
						<THead>
							<tr>
								{visibleColumns.includes('singer') && (
									<TableHeading>
										<TableHeadingSort
											form={sortFilterForm}
											sort={['full-name', 'last-name-first']}
											onClick={() => handleNameSort(sortFilterForm)}
											indicator={sortFilterForm.data.sort === 'full-name' ? 'First' : 'Last'}
										>
											Name
										</TableHeadingSort>
									</TableHeading>
								)}
								{visibleColumns.includes('voice_part') && (
									<TableHeading>Voice Part</TableHeading>
								)}
								{visibleColumns.includes('rsvp') && (
									<TableHeading>
										<TableHeadingSort form={sortFilterForm} sort="rsvp-response">
											RSVP
										</TableHeadingSort>
									</TableHeading>
								)}
								{visibleColumns.includes('updated') && (
									<TableHeading>
										<TableHeadingSort form={sortFilterForm} sort="rsvp-updated">
											Updated
										</TableHeadingSort>
									</TableHeading>
								)}
								{visibleColumns.includes('dietary') && (
									<TableHeading>
										<TableHeadingSort form={sortFilterForm} sort="dietary-medical">
											Dietary / Medical
										</TableHeadingSort>
									</TableHeading>
								)}
								{customFields
									.filter(cf => visibleColumns.includes(`cf-${cf.id}`))
									.map(cf => (
										<TableHeading key={cf.id}>{cf.name}</TableHeading>
									))}
							</tr>
						</THead>
						<TBody>
							{allSingers.map(singer => (
								<tr key={singer.id}>
								{visibleColumns.includes('singer') && (
									<TableCell>
										<div className="flex items-center space-x-3">
											<div className="shrink-0">
												<img
													className="h-8 w-8 rounded-md object-cover"
													src={singer.user.avatar_url}
													alt={singer.user.name}
												/>
											</div>
											<div>
												<div>
													<SingerCategoryTag
														status={new SingerStatus(singer.category.slug)}
													/>
													<Link
														href={route('singers.show', { singer })}
														className="ml-1 text-sm font-medium text-purple-600 hover:text-purple-700 focus:text-purple-700 hover:underline focus:underline"
													>
														{singer.user.name}
													</Link>
												</div>
												<div className="text-sm text-gray-500">
													<Icon icon="phone" mr className="text-gray-400" />
													{singer.user.phone ? (
														<a href={`tel:${singer.user.phone}`} target="_blank">
															{singer.user.phone}
														</a>
													) : (
														'No phone'
													)}
												</div>
											</div>
										</div>
									</TableCell>
								)}
								{visibleColumns.includes('voice_part') && (
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
								)}
								{visibleColumns.includes('rsvp') && (
									<TableCell>
										<RsvpTag
											icon={singer.rsvp.icon}
											label={singer.rsvp.label}
											colour={singer.rsvp.colour}
										/>
									</TableCell>
								)}
								{visibleColumns.includes('updated') && (
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
								)}
								{visibleColumns.includes('dietary') && (
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
													<span className="text-amber-800">
														{singer.user.medical_conditions}
													</span>
												</div>
											) : (
												<div className="text-gray-400">
													<span className="font-semibold">Medical: </span>
													None
												</div>
											)}
										</div>
									</TableCell>
								)}
								{customFields.filter(cf => visibleColumns.includes(`cf-${cf.id}`)).map(cf => {
									const field = singer.custom_fields.find(f => f.id === cf.id);

									return (
										<TableCell key={cf.id}>
											<span className="text-xs text-gray-500">{field?.entry?.value || '-'}</span>
										</TableCell>
									);
								})}
							</tr>
						))}
					</TBody>
				</Table>
			}
		/>
	</>
);
};

Index.layout = page => <TenantLayout children={page} />;

export default Index;
