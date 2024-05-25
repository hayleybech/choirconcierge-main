import React from 'react';
import {Link} from "@inertiajs/react";
import Swatch from "../../components/Swatch";
import Table, {TableCell} from "../../components/Table";
import DateTag from "../../components/DateTag";
import collect from "collect.js";
import useRoute from "../../hooks/useRoute";

const VoicePartTableDesktop = ({ voiceParts }) => {
    const { route } = useRoute();

    const headings = collect({
        title: 'Name',
        singers: 'Singers',
        created: 'Created',
    })

    return (
        <Table
            headings={headings}
            body={voiceParts.map((voicePart) => (
                <tr key={voicePart.id}>
                    <TableCell>
                        <div className="flex items-center">
                            <div>
                                <Swatch colour={voicePart.colour} />
                            </div>
                            <div className="ml-4">
                                <Link href={route('voice-parts.edit', {voice_part: voicePart.id})} className="text-sm font-medium text-purple-800">
                                    {voicePart.title}
                                </Link>
                            </div>
                        </div>
                    </TableCell>
                    <TableCell>
                        <Link href={route('singers.index')} data={{ filter: { 'enrolments.voice_part_id': [voicePart.id] } }} className="text-purple-800">
                            {voicePart.singers_count} {voicePart.singers_count === 1 ? 'singer' : 'singers'}
                        </Link>
                    </TableCell>
                    <TableCell>
                        <DateTag date={voicePart.created_at} />
                    </TableCell>
                </tr>
            ))}
        />
    );
}

export default VoicePartTableDesktop;