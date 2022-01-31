import React from 'react';
import {Link} from "@inertiajs/inertia-react";
import SongStatusTag from "../../components/SongStatusTag";
import PitchButton from "../../components/PitchButton";
import Table, {TableCell} from "../../components/Table";
import Badge from "../../components/Badge";
import DateTag from "../../components/DateTag";
import SongStatus from "../../SongStatus";

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

export default SongTableDesktop;