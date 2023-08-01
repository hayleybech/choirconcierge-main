import React from 'react';
import { Link, usePage } from "@inertiajs/inertia-react";
import Table, {TableCell} from "../../components/Table";
import collect from "collect.js";
import useRoute from "../../hooks/useRoute";
import Button from "../../components/inputs/Button";
import Icon from "../../components/Icon";

const SongCategoryTableDesktop = ({ categories, showEditCategory, showDeleteCategory }) => {
    const { route } = useRoute();

    const { can } = usePage().props;

    const headings = collect({
        name: 'Name',
        songs: 'Songs',
        actions: 'Actions',
    })

    return (
        <Table
            headings={headings}
            body={categories.map((category) => (
                <tr key={category.id}>
                    <TableCell>
                        <div className="flex items-center">
                            <div className="ml-4">
                                <div className="text-sm text-gray-700">
                                    {category.title}
                                </div>
                            </div>
                        </div>
                    </TableCell>
                    <TableCell>
                        <Link href={route('songs.index')} data={{ filter: { 'categories.id': [category.id] } }} className="text-purple-800">
                            {category.songs_count} {category.songs_count === 1 ? 'song' : 'songs'}
                        </Link>
                    </TableCell>
                  <TableCell>
                    <div className="flex gap-2 justify-end">
                      {can.create_song && (
                        <Button variant="primary" size="sm" onClick={() => showEditCategory(category)}>
                          <Icon icon="edit" />
                          Edit
                        </Button>
                      )}
                      {can.create_song && (
                        <Button variant="danger-outline" size="sm" onClick={() => showDeleteCategory(category)}>
                          <Icon icon="trash" />
                          Delete
                        </Button>
                      )}
                    </div>
                  </TableCell>
                </tr>
            ))}
        />
    );
}

export default SongCategoryTableDesktop;