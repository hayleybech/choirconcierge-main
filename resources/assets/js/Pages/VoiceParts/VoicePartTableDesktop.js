import React from 'react';
import {Link} from "@inertiajs/react";
import Swatch from "../../components/Swatch";
import Table, {TableCell, THead, TBody, TableHeading} from "../../components/Table";
import DateTag from "../../components/DateTag";
import collect from "collect.js";
import useRoute from "../../hooks/useRoute";

const VoicePartTableDesktop = ({ voiceParts }) => {
    const { route } = useRoute();

    const headings = collect({
        title: <TableHeading>Name</TableHeading>,
        singers: <TableHeading>Singers</TableHeading>,
        created: <TableHeading>Created</TableHeading>,
    })

    return (
        <Table>
            <THead>
                <tr>{headings.values().toArray()}</tr>
            </THead>
            <TBody>
                {voiceParts.map((voicePart) => (
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
                            <DateTag icon="pencil" date={voicePart.created_at} />
                        </TableCell>
                    </tr>
                ))}
            </TBody>
        </Table>
    );
}

export default VoicePartTableDesktop;