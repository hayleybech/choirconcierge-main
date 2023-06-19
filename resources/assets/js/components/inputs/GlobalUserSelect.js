import React from 'react';
import {components} from "react-select";
import axios from 'axios';
import AsyncCreatableSelect from "react-select/async-creatable";
import Icon from "../Icon";
import useRoute from "../../hooks/useRoute";

const GlobalUserSelect = ({ defaultValue, updateFn, multiple = false }) => {
    const { route } = useRoute();

    const load = (inputValue) => axios
        .get(route('global-find.users', { q: inputValue }))
        .then(response => response.data);

    return (
        <div className="mt-1">
            <AsyncCreatableSelect
                cacheOptions
                defaultOptions={[]}
                loadOptions={load}
                components={{ SingleValue, MultiValueLabel, Option }}
                isMulti={multiple}
                defaultValue={defaultValue}
                onChange={option => multiple ? updateFn(option.map(item => item.value)) : updateFn(option.value)}
                createOptionPosition="first"
                isValidNewOption={(inputValue, value, options) => ! options.some((option) => option.email === inputValue)}
            />
        </div>
    );
}

export default GlobalUserSelect;

const SingleValue = ({ data: { name, avatarUrl, email, value }, children, ...props }) => (
    <components.SingleValue {...props}>
        <div className="flex items-center">
            {name
                ? <img className="h-5 w-5 rounded-md inline-block" src={avatarUrl} alt={name}/>
                : (
                    <div className="h-5 w-5 rounded-md bg-gray-100 text-sm text-gray-400 flex justify-center items-center">
                        <Icon icon="user"/>
                    </div>
                )
            }
            <div className="text-gray-700 inline-block ml-2">{name ?? 'New User'}</div>
            <div className="text-gray-500 text-sm inline-block ml-2">{email ?? value}</div>
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

const Option = ({ data: { name, avatarUrl, email, label, value }, children, ...props}) => (
    <components.Option {...props}>
        <div className="flex items-center">
            {name
                ? <img className="h-10 w-10 rounded-md inline-block" src={avatarUrl} alt={name}/>
                : (
                    <div className="h-10 w-10 rounded-md bg-gray-100 text-gray-400 text-xl flex justify-center items-center">
                        <Icon icon="user"/>
                    </div>
                )
            }
            <div className="ml-4 flex flex-col">
                <span className="text-gray-700">{name ?? label}</span>
                <span className="text-gray-500 text-sm">{email ?? value}</span>
            </div>
        </div>
    </components.Option>
);