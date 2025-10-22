import React from 'react';
import TableMobile, { TableMobileListItem } from '../../components/TableMobile';
import Icon from '../../components/Icon';
import Button from '../../components/inputs/Button';
import { Link, usePage } from '@inertiajs/react';

const EventTypeTableMobile = ({ categories, showEditCategory, showDeleteCategory }) => {
	const { can } = usePage().props;

	return (
		<TableMobile>
			{categories.map(category => (
				<TableMobileListItem key={category.id}>
					<div className="flex items-center justify-between px-4 py-4 sm:px-6">
						<div className="flex items-center min-w-0 mr-1.5">
							<span className="text-sm text-gray-700 truncate">{category.title}</span>
						</div>
						<div className="flex items-center min-w-0 mr-1.5">
							<Link
								href={route('events.index')}
								data={{ filter: { 'type.id': [category.id], 'date': '' } }}
								className="text-purple-800 text-xs"
							>
								{category.events_count} {category.events_count === 1 ? 'event' : 'events'}
							</Link>
						</div>
						<div className="flex gap-2 justify-end">
							{can.create_event && (
								<Button variant="primary" size="sm" onClick={() => showEditCategory(category)}>
									<Icon icon="edit" />
									<div className="sr-only">Edit</div>
								</Button>
							)}
							{can.create_event && (
								<Button variant="danger-outline" size="sm" onClick={() => showDeleteCategory(category)}>
									<Icon icon="trash" />
									<div className="sr-only">Delete</div>
								</Button>
							)}
						</div>
					</div>
				</TableMobileListItem>
			))}
		</TableMobile>
	);
};

export default EventTypeTableMobile;
