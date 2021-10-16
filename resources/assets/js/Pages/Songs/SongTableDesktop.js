import React from 'react';
import {Link} from "@inertiajs/inertia-react";
import SongStatusTag from "../../components/SongStatusTag";
import PitchButton from "../../components/PitchButton";
import {DateTime} from "luxon";
import Table, {TableCell} from "../../components/Table";
import Badge from "../../components/Badge";

const SongTableDesktop = ({ songs }) => (
    <Table
        headings={['Name', 'Status', 'Category', 'created']}
        body={songs.map((song) => (
            <tr key={song.id}>
                <TableCell>
                    <div className="flex items-center">
                        <div>
                            <PitchButton note={song.pitch.split('/')[0]} size="sm" />
                        </div>
                        <div className="ml-4">
                            <Link href={route('songs.show', song.id)} className="text-sm font-medium text-purple-800">{song.title}</Link>
                        </div>
                    </div>
                </TableCell>
                <TableCell>
                    <SongStatusTag name={song.status.title} colour={song.status.colour} withLabel />
                </TableCell>
                <TableCell>
                    <div className="space-x-1.5 space-y-1.5">
                        {song.categories.map(category => (<Badge key={category.id}>{category.title}</Badge>))}
                    </div>
                </TableCell>
                <TableCell>
                    {DateTime.fromJSDate(new Date(song.created_at)).toLocaleString(DateTime.DATE_MED)}
                </TableCell>
            </tr>
        ))}
    />
);

export default SongTableDesktop;