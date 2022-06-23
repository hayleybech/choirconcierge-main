import React from 'react';
// import {Link} from "@inertiajs/inertia-react";
// import {DateTime} from "luxon";
import Table, {TableCell} from "../../../components/Table";
import {Input} from "postcss";
import TextInput from "../../../components/inputs/TextInput";
import Button from "../../../components/inputs/Button";
import Icon from "../../../components/Icon";
import Select from "../../../components/inputs/Select";
import SingerSelect from "../../../components/inputs/SingerSelect";
// import Badge from "../../components/Badge";
// import DateTag from "../../components/DateTag";

const ActivityTableDesktop = ({ activities }) => (
    <Table
        headings={['Order', 'Start Time', 'Type', 'Song', 'Assignee', 'Notes', 'Duration', 'Actions']}
        body={<>
            {activities.map((activity) => (
            <tr key={activity.id}>
                {/*<TableCell>*/}
                {/*    <div className="flex items-center">*/}
                {/*        <div className="ml-4">*/}
                {/*            <Link href={route('events.show', event.id)} className="text-sm font-medium text-purple-800">{event.title}</Link>*/}
                {/*        </div>*/}
                {/*    </div>*/}
                {/*</TableCell>*/}
                {/*<TableCell>*/}
                {/*    <Badge>{event.type.title}</Badge>*/}
                {/*</TableCell>*/}
                {/*<TableCell>*/}
                {/*    <DateTag date={event.call_time} />*/}
                {/*</TableCell>*/}
                {/*<TableCell>*/}
                {/*    {event.location_name}*/}
                {/*</TableCell>*/}
                {/*<TableCell>*/}
                {/*    {DateTime.fromJSDate(new Date(event.call_time)) < DateTime.now()*/}
                {/*        ? <p>{event.present_count}&nbsp;present</p>*/}
                {/*        : <p>{event.going_count}&nbsp;going</p>*/}
                {/*    }*/}
                {/*</TableCell>*/}
                {/*<TableCell>*/}
                {/*    <DateTag date={event.created_at} />*/}
                {/*</TableCell>*/}
            </tr>
            ))}
            <tr>
                <td className="flex space-x-1.5">
                    <TextInput type="number" />
                    <Button size="xs"><Icon icon="arrow-up" /></Button>
                    <Button size="xs"><Icon icon="arrow-down" /></Button>
                </td>
                <td>00:00</td>
                <td><Select options={[{ key: 0, label: 'Activity Type' }]} /></td>
                <td><Select options={[{ key: 0, label: 'Song' }]} /></td>
                <td><SingerSelect /></td>
                <td><TextInput /></td>
                <td><TextInput type="number" /></td>
                <td><Button size="sm">Save</Button></td>
            </tr>
        </>}
    />
);

export default ActivityTableDesktop;