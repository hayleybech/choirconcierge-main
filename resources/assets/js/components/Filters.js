import React, {useEffect, useRef} from 'react';
import Icon from "./Icon";
import Button from "./inputs/Button";
import SectionSubtitle from "./SectionSubtitle";

const Filters = ({ routeName, form: { submit, data, setData }, render }) => {
    const firstUpdate = useRef(true);
    useEffect(() => {
        if (firstUpdate.current) {
            firstUpdate.current = false;
            return;
        }

        submit();
    }, [data]);

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