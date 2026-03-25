import React from 'react';
import { Link, usePage } from "@inertiajs/react";
import Table, {TableCell, THead, TBody, TableHeading} from "../../components/Table";
import collect from "collect.js";
import useRoute from "../../hooks/useRoute";
import Button from "../../components/inputs/Button";
import Icon from "../../components/Icon";

const EventTypeTableDesktop = ({ categories, showEditCategory, showDeleteCategory }) => {
    const { route } = useRoute();

    const { can } = usePage().props;

    const headings = collect({
        name: <TableHeading>Name</TableHeading>,
        events: <TableHeading>Events</TableHeading>,
        actions: <TableHeading>Actions</TableHeading>,
    })

    return (
		<Table>
			<THead>
				<tr>{headings.values().toArray()}</tr>
			</THead>
			<TBody>
				{categories.map(category => (
					<tr key={category.id}>
						<TableCell>
							<div className="flex items-center">
								<span className="text-sm text-gray-700">{category.title}</span>
							</div>
						</TableCell>
						<TableCell>
							<Link
								href={route('events.index')}
								data={{ filter: { 'type.id': [category.id], 'date': '' } }}
								className="text-purple-800"
							>
								{category.events_count} {category.events_count === 1 ? 'event' : 'events'}
							</Link>
						</TableCell>
						<TableCell>
							<div className="flex gap-2 justify-end">
								{can.create_event && (
									<Button variant="primary" size="xs" onClick={() => showEditCategory(category)}>
										<Icon icon="edit" />
										Edit
									</Button>
								)}
								{can.create_event && (
									<Button variant="danger-outline" size="xs" onClick={() => showDeleteCategory(category)}>
										<Icon icon="trash" />
										Delete
									</Button>
								)}
							</div>
						</TableCell>
					</tr>
				))}
			</TBody>
		</Table>
	);
}

export default EventTypeTableDesktop;