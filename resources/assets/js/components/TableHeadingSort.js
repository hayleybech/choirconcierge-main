import React, {useEffect, useRef} from "react";
import classNames from "../classNames";
import Icon from "./Icon";

const TableHeadingSort = ({ form: { submit, data, setData }, sort, children, onClick, indicator }) => {
    const isActive = data.sort === sort || (Array.isArray(sort) && sort.includes(data.sort));
    const sortDir = isActive ? data.sortDir : 'asc';

    const firstUpdate = useRef(true);
    useEffect(() => {
        if (firstUpdate.current) {
            firstUpdate.current = false;
            return;
        }

        submit();
    }, [data]);

    return (
        <button
            onClick={onClick ?? (() => setData(data => ({ ...data, sort, sortDir: sortDir === 'asc' ? 'desc' : 'asc' })))}
            className="group text-left text-xs font-medium text-gray-500 uppercase tracking-wider flex items-center"
        >
			<span className="shrink-0">
            	{children}
			</span>
            {isActive && indicator && (
                <span className="ml-1 text-[10px] text-gray-400 font-normal normal-case">
                    ({indicator})
                </span>
            )}
            <span className={classNames('ml-2 flex-none rounded p-0.5',
                isActive
                    ? ' bg-gray-200 text-gray-900 group-hover:bg-gray-300'
                    : ' group-hover:bg-gray-200 group-focus:bg-gray-200 text-gray-400 group-hover:text-gray-900 group-focus:text-gray-900'
            )}
            >
              <Icon icon={sortDir === 'desc' ? 'chevron-down' : 'chevron-up'} aria-hidden="true" />
            </span>
        </button>
    );
}

export default TableHeadingSort;