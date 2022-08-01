import React from 'react';
import SectionHeader from "./SectionHeader";
import SectionTitle from "./SectionTitle";
import Button from "./inputs/Button";
import Icon from "./Icon";

const FilterSortPane = ({ sorts, filters, closeFn }) => (
    <div className="bg-white p-5 border-b border-gray-300">
        <SectionHeader>
            <SectionTitle>Filter/Sort</SectionTitle>

            <Button onClick={closeFn} variant="clear" size="sm"><Icon icon="times" /></Button>
        </SectionHeader>

        {sorts}
        {filters}
    </div>
);

export default FilterSortPane;