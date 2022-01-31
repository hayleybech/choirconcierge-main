import React, {useEffect, useRef} from 'react';
import {useForm} from "@inertiajs/inertia-react";
import SectionHeader from "./SectionHeader";
import SectionTitle from "./SectionTitle";
import Button from "./inputs/Button";
import Icon from "./Icon";
import RadioGroup from "./inputs/RadioGroup";
import Label from "./inputs/Label";
import SectionSubtitle from "./SectionSubtitle";
import collect from "collect.js";

const Sorts = ({ routeName, options }) => {
    const params = new URLSearchParams(location.search);

    const { data, setData, get, transform } = useForm({
        sort: params.has('sort')
            ? params.get('sort').replace(/^-/, '')
            : options.find(option => option.default).id,
        sortDir: params.get('sort')?.startsWith('-') ? 'desc' : 'asc',
    });

    function submit(e) {
        e?.preventDefault();

        get(route(routeName));
    }

    const firstUpdate = useRef(true);
    useEffect(() => {
        if (firstUpdate.current) {
            firstUpdate.current = false;
            return;
        }

        submit();
    }, [data])

    const filters = () => {
        const urlFilters = {};
        collect(Array.from(params.keys()))
            .unique()
            .filter(key => key.includes('filter'))
            .each(key => {
                const cleanKey = key.slice(
                    key.indexOf('[') + 1,
                    key.indexOf(']'),
                );

                urlFilters[cleanKey] = params.getAll(key);
            });
        return urlFilters;
    };

    transform((data) => ({
        sort: data.sortDir === 'desc' ? `-${data.sort}` : data.sort,
        filter: {
            ...filters(),
        }
    }));

    return (
        <form onSubmit={submit}>
            <SectionSubtitle>Sort</SectionSubtitle>

            <div className="flex flex-col items-stretch space-y-4 mb-4">
                <div>
                    <RadioGroup
                        label={<Label label="Sort By" />}
                        options={options}
                        selected={data.sort}
                        setSelected={value => setData('sort', value)}
                        vertical
                    />
                </div>

                <div>
                    <RadioGroup
                        label={<Label label="Sort Direction" />}
                        options={[
                            { id: 'asc', name: 'Asc', icon: 'sort-amount-up' },
                            { id: 'desc', name: 'Desc', icon: 'sort-amount-down' },
                        ]}
                        selected={data.sortDir}
                        setSelected={value => setData('sortDir', value)}
                        vertical
                    />
                </div>
            </div>
        </form>
    );
};

export default Sorts;
