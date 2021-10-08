import React from 'react';
import {Link} from "@inertiajs/inertia-react";
import {DateTime} from "luxon";
import Swatch from "../../components/Swatch";
import Table, {TableCell} from "../../components/Table";

const VoicePartTableDesktop = ({ voiceParts }) => (
    <Table
        headings={['Name', 'Singers', 'Created']}
        body={voiceParts.map((voicePart) => (
            <tr key={voicePart.id}>
                <TableCell>
                    <div className="flex items-center">
                        <div>
                            <Swatch colour={voicePart.colour} />
                        </div>
                        <div className="ml-4">
                            <Link href={route('voice-parts.edit', voicePart.id)} className="text-sm font-medium text-purple-800">{voicePart.title}</Link>
                        </div>
                    </div>
                </TableCell>
                <TableCell>
                    {voicePart.singers_count}
                </TableCell>
                <TableCell>
                    {DateTime.fromJSDate(new Date(voicePart.created_at)).toLocaleString(DateTime.DATE_MED)}
                </TableCell>
            </tr>
        ))}
    />
);

export default VoicePartTableDesktop;