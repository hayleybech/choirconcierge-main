import React from 'react';
import {Link} from "@inertiajs/react";
import SongStatusTag from "../../components/SongStatusTag";
import PitchButton from "../../components/PitchButton";
import Table, {TableCell} from "../../components/Table";
import Badge from "../../components/Badge";
import DateTag from "../../components/DateTag";
import SongStatus from "../../SongStatus";
import collect from "collect.js";
import TableHeadingSort from "../../components/TableHeadingSort";
import useRoute from "../../hooks/useRoute";
import Pagination from '../../components/Pagination';
import {useInstrument} from "../../hooks/useInstrument";

const SongTableDesktop = ({ songs, sortFilterForm }) => {
    const { route } = useRoute();

    const [instrument] = useInstrument();

    const headings = collect({
        title: <TableHeadingSort form={sortFilterForm} sort="title">Title</TableHeadingSort>,
        status: <TableHeadingSort form={sortFilterForm} sort="status-title">Status</TableHeadingSort>,
        category: 'Category',
        created_at: <TableHeadingSort form={sortFilterForm} sort="created_at">Date Created</TableHeadingSort>,
    });

    return (
        <Table
            pagination={<Pagination details={songs} />}
            headings={headings}
            body={songs.data.map((song) => (
                <tr key={song.id}>
                    <TableCell>
                        <div className="flex items-center">
                            <div>
                                <PitchButton instrument={instrument} note={song.pitch.split('/')[0]} size="xs" />
                            </div>
                            <div className="ml-4">
                                <Link href={route('songs.show', {song})} className="text-sm font-medium text-purple-800">{song.title}</Link>
                            </div>
                        </div>
                    </TableCell>
                    <TableCell>
                        <SongStatusTag status={new SongStatus(song.status.slug)} withLabel />
                    </TableCell>
                    <TableCell>
                        <div className="space-x-1.5 space-y-1.5">
                            {song.categories.map(category => (<Badge key={category.id}>{category.title}</Badge>))}
                        </div>
                    </TableCell>
                    <TableCell>
                        <DateTag icon="pencil" date={song.created_at} />
                    </TableCell>
                </tr>
            ))}
        />
    );
}

export default SongTableDesktop;