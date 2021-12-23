import React from "react";

export const TableHeading = ({ colSpan, children }) => (
    <th
        colSpan={colSpan}
        scope="col"
        className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
    >
        {children}
    </th>
);

export const TableCell = ({ colSpan, children }) => (
    <td colSpan={colSpan} className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
        {children}
    </td>
);

const Table = ({ headings, body }) => (
    <div className="-my-2 overflow-x-auto">
        <div className="py-2 align-middle inline-block min-w-full">
            <div className="shadow overflow-hidden border-b border-gray-200">
                <table className="min-w-full divide-y divide-gray-200">
                    <thead className="bg-gray-50">
                        <tr>
                            {headings.map((heading) => (<TableHeading key={heading}>{heading}</TableHeading>))}
                        </tr>
                    </thead>
                    <tbody className="bg-white divide-y divide-gray-200">
                        {body}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
);

export default Table;
