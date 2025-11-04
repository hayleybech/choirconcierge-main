import React from "react";
import classNames from "../../../classNames";

export const DetailList = ({ gridCols = 'sm:grid-cols-2 md:grid-cols-4', children }) => (
    <dl className={classNames("grid grid-cols-1 gap-x-4 gap-y-8", gridCols)}>
        {children}
    </dl>
);

export const DetailListItem = (props) => (
    <div className={props.colClass ?? 'sm:col-span-1'}>
        <dt className="text-sm font-medium text-gray-500">
            {props.label}
        </dt>
        <dd className="mt-1 text-sm text-gray-900">
            {props.children}
        </dd>
    </div>
)