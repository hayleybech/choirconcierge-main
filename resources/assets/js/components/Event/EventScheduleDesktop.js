import React from 'react';
import Table, {TableCell} from "../Table";

const EventScheduleDesktop = ({ event }) => (
    <Table
        headings={['Description', 'Duration']}
        body={<>
            {event.activities.map((item) => (
                <tr key={item.id}>
                    <TableCell>
                        {item.description}
                    </TableCell>
                    <TableCell>
                        {item.duration} min
                    </TableCell>
                </tr>
            ))}
            <tr>
                <TableCell />
                <TableCell>
                    Total: {event.activities.reduce((prevValue, item) => prevValue + item.duration, 0)} min
                </TableCell>
            </tr>
        </>}
    />
);

export default EventScheduleDesktop;