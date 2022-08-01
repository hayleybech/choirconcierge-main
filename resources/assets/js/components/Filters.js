import React, {useEffect, useRef} from 'react';
import Icon from "./Icon";
import Button from "./inputs/Button";
import SectionSubtitle from "./SectionSubtitle";
import useSortFilterForm from "../hooks/useSortFilterForm";

const Filters = ({ routeName, filters, sorts, transforms, render }) => {
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