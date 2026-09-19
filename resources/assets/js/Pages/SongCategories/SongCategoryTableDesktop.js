import React from 'react';
import { Link, usePage } from "@inertiajs/react";
import Table, {TableCell, THead, TBody, TableHeading} from "../../components/Table";
import useRoute from "../../hooks/useRoute";
import Button from "../../components/inputs/Button";
import Icon from "../../components/Icon";

const SongCategoryTableDesktop = ({ categories, showEditCategory, showDeleteCategory }) => {
    const { route } = useRoute();

    const { can } = usePage().props;

    return (
        <Table>
            <THead>
                <tr>
                    <TableHeading>Name</TableHeading>
                    <TableHeading>Songs</TableHeading>
                    <TableHeading>Actions</TableHeading>
                </tr>
            </THead>
            <TBody>
                {categories.map((category) => (
                    <tr key={category.id}>
                        <TableCell>
                            <div className="flex items-center">
                                <span className="text-sm text-gray-700">
                                    {category.title}
                                </span>
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
                      </TableCell>
                    </tr>
                ))}
            </TBody>
        </Table>
    );
}

export default SongCategoryTableDesktop;