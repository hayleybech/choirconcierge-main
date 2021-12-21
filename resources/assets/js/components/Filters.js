import React, {useEffect, useRef} from 'react';
import {useForm} from "@inertiajs/inertia-react";
import Icon from "./Icon";
import Button from "./inputs/Button";
import SectionSubtitle from "./SectionSubtitle";

const Filters = ({ routeName, fields, transforms = () => {}, render }) => {
    const params = new URLSearchParams(location.search);

    const { data, setData, get, transform } = useForm(fields(params));

    function submit(e) {
        e?.preventDefault();

        get(route(routeName));
    }

    transform((data) => ({
        filter: {
            ...data,
            ...transforms(data),
        }
    }));

    const firstUpdate = useRef(true);
    useEffect(() => {
        if (firstUpdate.current) {
            firstUpdate.current = false;
            return;
        }

        submit();
    }, [data])

    return (
        <form onSubmit={submit}>
            <SectionSubtitle>Filter</SectionSubtitle>

            <div className="flex flex-col items-stretch space-y-4 mb-4">
                {render(data, setData)}

                <Button variant="danger-outline" href={route(routeName)} size="sm">
                    <Icon icon="times" />
                    Clear
                </Button>
            </div>
        </form>
    );
}

export default Filters;