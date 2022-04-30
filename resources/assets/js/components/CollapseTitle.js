import Icon from "./Icon";
import React from "react";

const CollapseTitle = ({ open, children }) => (
    <h2 className="text-lg leading-6 font-semibold text-gray-700">
        <Icon icon={open ? 'chevron-up' : 'chevron-down'} className="mr-2" />
        { children }
    </h2>
);

export default CollapseTitle;