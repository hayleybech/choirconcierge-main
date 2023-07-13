import React from 'react';
import {components} from "react-select";
import AsyncSelect from 'react-select/async';
import axios from 'axios';
import Badge from "../Badge";
import useRoute from "../../hooks/useRoute";

const SingerSelect = ({ defaultValue, updateFn, multiple = false }) => {
    const { route } = useRoute();

    const load = (inputValue) => axios
        .get(route('find.singers', { q: inputValue }))
        .then(response => response.data);

    return (
        <div className="mt-1">
            <AsyncSelect
                cacheOptions
                defaultOptions={[]}
                loadOptions={load}
                components={{ SingleValue, MultiValueLabel, Option }}
                isMulti={multiple}
                defaultValue={defaultValue}
                onChange={option => multiple ? updateFn(option.map(item => item.value)) : updateFn(option.value)}
                placeholder="Start typing..."
            />
        </div>
    );
}

export default SingerSelect;

const SingleValue = ({ data: { name, avatarUrl }, children, ...props }) => (
    <components.SingleValue {...props}>
        <div className="flex items-center">
            <img className="h-5 w-5 rounded-md inline-block" src={avatarUrl} alt={name} />
            <div className="text-gray-700 inline-block ml-2">{name}</div>
        </div>
    </components.SingleValue>
);

const MultiValueLabel = ({ data: { name, avatarUrl }, children, ...props }) => (
    <components.MultiValueLabel {...props}>
        <div className="flex items-center">
            <img className="h-5 w-5 rounded-md inline-block" src={avatarUrl} alt={name} />
            <div className="text-gray-700 inline-block ml-2">{name}</div>
        </div>
    </components.MultiValueLabel>
);

const Option = ({ data: { name, avatarUrl, email, roles }, children, ...props}) => (
    <components.Option {...props}>
        <div className="flex items-center">
            <img className="h-10 w-10 rounded-md inline-block" src={avatarUrl} alt={name} />
            <div className="ml-4 flex flex-col">
                <div className="flex flex-row items-center">
                    <span className="text-gray-700">{name}</span>
                    <span className="ml-2 text-gray-500 text-sm">({email})</span>
                </div>
                {roles.length > 0 && (
                    <div className="space-x-1.5 space-y-1.5">
                        {roles.map(role => <Badge key={role.name}>{role.name.split(' ')[0]}</Badge>)}
                    </div>
                )}
            </div>
        </div>
    </components.Option>
);