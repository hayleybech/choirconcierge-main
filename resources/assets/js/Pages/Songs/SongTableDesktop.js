import React, {useState} from 'react';
import {Link} from "@inertiajs/inertia-react";
import SongStatusTag from "../../components/SongStatusTag";
import PitchButton from "../../components/PitchButton";
import Table, {TableCell} from "../../components/Table";
import Badge from "../../components/Badge";
import DateTag from "../../components/DateTag";
import SongStatus from "../../SongStatus";
import collect from "collect.js";
import TableHeadingSort from "../../components/TableHeadingSort";
import {Synth} from "tone";

const SongTableDesktop = ({ songs, sortFilterForm }) => {
    const [synth] = useState(() => new Synth().toDestination());
    const headings = collect({
        title: <TableHeadingSort form={sortFilterForm} sort="title">Title</TableHeadingSort>,
        status: <TableHeadingSort form={sortFilterForm} sort="status-title">Status</TableHeadingSort>,
        category: 'Category',
        created_at: <TableHeadingSort form={sortFilterForm} sort="created_at">Date Created</TableHeadingSort>,
    });

    return (
        <Table
            headings={headings}
            body={songs.map((song) => (
                <tr key={song.id}>
                    <TableCell>
                        <div className="flex items-center">
                            <div>
                                <PitchButton synth={synth} note={song.pitch.split('/')[0]} size="sm" />
                            </div>
                            <div className="ml-4">
                                <Link href={route('songs.show', song.id)} className="text-sm font-medium text-purple-800">{song.title}</Link>
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
                        <DateTag date={song.created_at} />
                    </TableCell>
                </tr>
            ))}
        />
    );
}

export default SongTableDesktop;