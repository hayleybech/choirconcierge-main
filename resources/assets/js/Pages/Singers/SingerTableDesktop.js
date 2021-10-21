import React from 'react';
import {Link} from "@inertiajs/inertia-react";
import VoicePartTag from "../../components/VoicePartTag";
import SingerCategoryTag from "../../components/SingerCategoryTag";
import Table, {TableCell} from "../../components/Table";
import Icon from "../../components/Icon";

const SingerTableDesktop = ({ singers }) => (
    <Table
        headings={['Name', 'Voice Part', 'Status', 'Email']}
        body={singers.map((singer) => (
            <tr key={singer.id}>
                <TableCell>
                    <div className="flex items-center">
                        <div className="flex-shrink-0 h-10 w-10">
                            <img className="h-10 w-10 rounded-md" src={singer.user.avatar_url} alt={singer.user.name} />
                        </div>
                        <div className="ml-4">
                            <Link href={route('singers.show', singer.id)} className="text-sm font-medium text-purple-800">{singer.user.name}</Link>
                            <div>
                                <Icon icon="phone" mr className="text-gray-400" />
                                {singer.user.phone ? <a href={`tel:${singer.user.phone}`}>{singer.user.phone}</a> : 'No phone'}
                            </div>
                        </div>
                    </div>
                </TableCell>
                <TableCell>
                    {singer.voice_part && <VoicePartTag title={singer.voice_part.title} colour={singer.voice_part.colour} />}
                </TableCell>
                <TableCell>
                    <SingerCategoryTag name={singer.category.name} colour={singer.category.colour} withLabel />
                </TableCell>
                <TableCell>
                    <Icon icon="envelope" mr className="text-gray-400" />
                    <span>{singer.user.email}</span>
                </TableCell>
            </tr>
        ))}
    />
);

export default SingerTableDesktop;