import React from 'react';
import {Link, usePage} from "@inertiajs/inertia-react";
import VoicePartTag from "../../components/VoicePartTag";
import SingerCategoryTag from "../../components/SingerCategoryTag";
import Table, {TableCell} from "../../components/Table";
import Icon from "../../components/Icon";
import SingerStatus from "../../SingerStatus";
import FeeStatus from "../../components/FeeStatus";

const SingerTableDesktop = ({ singers }) => {
    const { can } = usePage().props;

    return (
        <Table
            headings={['Name', 'Voice Part', 'Status', 'Email', 'Fees'].filter((item) => item !== 'Fees' || can['manage_finances'])}
            body={singers.map((singer) => (
                <tr key={singer.id}>
                    <TableCell>
                        <div className="flex items-center">
                            <div className="shrink-0 h-10 w-10">
                                <img className="h-10 w-10 rounded-md" src={singer.user.avatar_url} alt={singer.user.name} />
                            </div>
                            <div className="ml-4">
                                <Link href={route('singers.show', singer.id)} className="text-sm font-medium text-purple-800">{singer.user.name}</Link>
                                <div>
                                    <Icon icon="phone" mr className="text-gray-400" />
                                    {singer.user.phone ? <a href={`tel:${singer.user.phone}`} target="_blank">{singer.user.phone}</a> : 'No phone'}
                                </div>
                            </div>
                        </div>
                    </TableCell>
                    <TableCell>
                        {singer.voice_part && <VoicePartTag title={singer.voice_part.title} colour={singer.voice_part.colour} />}
                    </TableCell>
                    <TableCell>
                        <SingerCategoryTag status={new SingerStatus(singer.category.slug)} withLabel />
                    </TableCell>
                    <TableCell>
                        <Icon icon="envelope" mr className="text-gray-400" />
                        <a href={`mailto:${singer.user.email}`} target="_blank">{singer.user.email}</a>
                    </TableCell>
                    {can['manage_finances'] && (
                    <TableCell>
                        <FeeStatus status={singer.fee_status} />
                    </TableCell>
                    )}
                </tr>
            ))}
        />
    );
}

export default SingerTableDesktop;