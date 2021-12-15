import React from 'react';
import {useForm} from "@inertiajs/inertia-react";
import Icon from "./Icon";
import Button from "./inputs/Button";
import SectionHeader from "./SectionHeader";
import SectionTitle from "./SectionTitle";

const Filters = ({ routeName, fields, transforms = () => {}, render, onClose }) => {
    const params = new URLSearchParams(location.search);

    const { data, setData, get, processing, transform } = useForm(fields(params));

    function submit(e) {
        e.preventDefault();

        get(route(routeName));
    }

    transform((data) => ({
        filter: {
            ...data,
            ...transforms(data),
        }
    }));

    return (
        <form onSubmit={submit}>
            <div className="bg-white p-5 flex flex-col items-stretch space-y-4 border-b border-gray-300">
                <SectionHeader>
                    <SectionTitle>Filter</SectionTitle>

                    <Button onClick={onClose} variant="clear" size="sm"><Icon icon="times" /></Button>
                </SectionHeader>

                {render(data, setData)}

                <div className="flex gap-x-2">
                    <Button variant="primary" type="submit" size="sm" disabled={processing} className="flex-grow">
                        <Icon icon="check" />
                        Filter
                    </Button>
                    <Button variant="danger-outline" href={route(routeName)} size="sm">
                        <Icon icon="times" />
                        Clear
                    </Button>
                </div>
            </div>
        </form>
    );
}

export default Filters;