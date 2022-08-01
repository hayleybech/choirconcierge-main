import React, {useEffect, useRef} from 'react';
import RadioGroup from "./inputs/RadioGroup";
import Label from "./inputs/Label";
import SectionSubtitle from "./SectionSubtitle";
import useSortFilterForm from "../hooks/useSortFilterForm";

const Sorts = ({ routeName, sorts, filters, transforms }) => {
    const { data, setData, submit } = useSortFilterForm(routeName, filters, sorts, transforms);

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
            <SectionSubtitle>Sort</SectionSubtitle>

            <div className="flex flex-col items-stretch space-y-4 mb-4">
                <div>
                    <RadioGroup
                        label={<Label label="Sort By" />}
                        options={sorts}
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
