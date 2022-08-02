import React from 'react';
import SectionHeader from "./SectionHeader";
import SectionTitle from "./SectionTitle";
import Button from "./inputs/Button";
import Icon from "./Icon";
import {useMediaQuery} from "react-responsive";

const FilterSortPane = ({ sorts, filters, closeFn }) => {
    const isDesktop = useMediaQuery({ query: '(min-width: 1024px)' });

    return (
        <div className="bg-white p-5 border-b border-gray-300">
            <SectionHeader>
                <SectionTitle>{isDesktop ? 'Filter' : 'Filter/Sort'}</SectionTitle>

                <Button onClick={closeFn} variant="clear" size="sm"><Icon icon="times" /></Button>
            </SectionHeader>

            {isDesktop || sorts}
            {filters}
        </div>
    );
}

export default FilterSortPane;