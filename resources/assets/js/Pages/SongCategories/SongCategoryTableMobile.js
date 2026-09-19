import React from 'react';
import TableMobile, { TableMobileHeader, TableMobileListItem } from '../../components/TableMobile';
import Icon from '../../components/Icon';
import Button from '../../components/inputs/Button';
import { Link, usePage } from '@inertiajs/react';
import useRoute from '../../hooks/useRoute';

const SongCategoryTableMobile = ({ categories, showEditCategory, showDeleteCategory }) => {
	const { can } = usePage().props;
	const { route } = useRoute();

	const bulkEdit = {
		isActiveMobile: false,
		noun: 'Song Category',
		selectedIds: [],
		totalItems: categories.length,
	};

	return (
		<div>
			<TableMobileHeader bulkEdit={bulkEdit} />
			<TableMobile>
				{categories.map(category => (
					<TableMobileListItem key={category.id}>
						<div className="flex items-center justify-between px-4 py-4 sm:px-6 gap-2">
							<div className="flex items-center min-w-0 gap-2">
								<span className="text-sm text-gray-700 truncate">{category.title}</span>
								<Link
									href={route('songs.index')}
									data={{ filter: { 'categories.id': [category.id] } }}
									className="text-purple-800 text-xs"
								>
									{category.songs_count} {category.songs_count === 1 ? 'song' : 'songs'}
								</Link>
							</div>
							<div className="flex gap-2 justify-end">
								{can.create_song && (
									<Button variant="primary" size="xs" onClick={() => showEditCategory(category)}>
										<Icon icon="edit" />
										Edit
									</Button>
								)}
								{can.create_song && (
									<Button variant="danger-outline" size="xs" onClick={() => showDeleteCategory(category)}>
										<Icon icon="trash" />
										Delete
									</Button>
								)}
							</div>
						</div>
					</TableMobileListItem>
				))}
			</TableMobile>
		</div>
	);
};

export default SongCategoryTableMobile;
