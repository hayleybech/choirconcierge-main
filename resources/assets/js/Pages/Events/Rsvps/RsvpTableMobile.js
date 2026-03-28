import React from 'react';

import TableMobile, { TableMobileHeader, TableMobileListItem } from '../../../components/TableMobile';
import Pagination from '../../../components/Pagination';
import RsvpTag from '../../../components/Event/RsvpTag';
import Badge from '../../../components/Badge';
import VoicePartTag from '../../../components/VoicePartTag';
import useRoute from '../../../hooks/useRoute';
import { Link } from '@inertiajs/react';
import SingerStatus from '../../../SingerStatus';
import SingerCategoryTag from '../../../components/SingerCategoryTag';
import Icon from '../../../components/Icon';
import DateTag from '../../../components/DateTag';
import Button from '../../../components/inputs/Button';

const RsvpTableMobile = ({ singers, pagination, showEnsemble, visibleColumns, customFields, columnsMenu, hasNonDefaultFilters, setShowFilters }) => {
	const { route } = useRoute();

	const bulkEdit = {
		isActiveMobile: false,
		noun: 'Singer',
		selectedIds: [],
		totalItems: singers.length,
	};

	return (
		<div>
			<TableMobileHeader bulkEdit={bulkEdit}>
				{columnsMenu}
				<Button
					variant={hasNonDefaultFilters ? 'success-outline' : 'clear-v2'}
					size="xs"
					onClick={() => setShowFilters(prev => !prev)}
				>
					<Icon icon="filter" mr />
					Filter/Sort
				</Button>
			</TableMobileHeader>
			<TableMobile pagination={<Pagination details={pagination} />}>
				{singers.map(singer => (
					<TableMobileListItem key={singer.id} url={route('singers.show', { singer: singer.id })}>
						<div className="flex items-center px-4 py-4 sm:px-6 gap-4">
							<div className="shrink-0">
								<img
									className="h-8 w-8 rounded-md object-cover"
									src={singer.user.avatar_url}
									alt={singer.user.name}
								/>
							</div>
							<div className="min-w-0 flex-1">
								<div className="flex items-center justify-between mb-1">
									<div>
										<div>
											<SingerCategoryTag status={new SingerStatus(singer.category.slug)} />
											<Link
												href={route('singers.show', { singer })}
												className="ml-1 text-sm font-medium text-purple-600 truncate"
											>
												{singer.user.name}
											</Link>
										</div>
										<div className="text-sm text-gray-500">
											<Icon icon="phone" mr className="text-gray-400 text-sm" />
											{singer.user.phone ? (
												<a href={`tel:${singer.user.phone}`} target="_blank">
													{singer.user.phone}
												</a>
											) : (
												'No phone'
											)}
										</div>
									</div>
									<div className="div">
										{visibleColumns.includes('rsvp') && (
											<div className="scale-90 origin-right">
												<RsvpTag
													icon={singer.rsvp.icon}
													label={singer.rsvp.label}
													colour={singer.rsvp.colour}
												/>
											</div>
										)}
										{visibleColumns.includes('updated') && !!singer.rsvp.updated_at && (
											<DateTag
												icon="pencil"
												date={singer.rsvp.updated_at}
												format="DATE_SHORT"
												className="text-gray-400 text-sm"
												mr={false}
											/>
										)}
									</div>
								</div>
								{visibleColumns.includes('voice_part') && (
									<div className="flex flex-wrap gap-1">
										{singer.enrolments.map(enrolment => (
											<div key={enrolment.id} className="flex gap-1 items-center">
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
											</div>
										))}
									</div>
								)}
								{visibleColumns.includes('dietary') &&
									(singer.user.dietary_requirements || singer.user.medical_conditions) && (
										<div className="mt-2 text-[11px] text-amber-800 bg-amber-50 py-1.5 px-2 rounded space-y-0.5">
											{singer.user.dietary_requirements && (
												<div className="truncate">
													<span className="font-bold uppercase text-[9px]">Dietary:</span>{' '}
													{singer.user.dietary_requirements}
												</div>
											)}
											{singer.user.medical_conditions && (
												<div className="truncate">
													<span className="font-bold uppercase text-[9px]">Medical:</span>{' '}
													{singer.user.medical_conditions}
												</div>
											)}
										</div>
									)}
								<div className="flex flex-col">
									{customFields
										.filter(cf => visibleColumns.includes(`cf-${cf.id}`))
										.map(cf => {
											const field = singer.custom_fields.find(f => f.id === cf.id);

											return (
												<div key={cf.id} className="leading-[1]">
													<span className="text-xs text-gray-500 font-bold">{cf.name}: </span>
													<span className="text-xs text-gray-500">
														{field?.entry?.value || '-'}
													</span>
												</div>
											);
										})}
								</div>
							</div>
						</div>
					</TableMobileListItem>
				))}
			</TableMobile>
		</div>
	);
};

export default RsvpTableMobile;
