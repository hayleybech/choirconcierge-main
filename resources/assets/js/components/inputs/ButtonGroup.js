import React from "react";

const ButtonGroup = ({ options }) => (
    <span className="relative z-0 inline-flex shadow-sm rounded-md mt-1">
        {options.map((option, index) => {
            if(index === 0) return (
                <ButtonGroupButton key={option.label} onClick={option.onClick} className="rounded-l-md">{option.label}</ButtonGroupButton>
            );

            if(index === (options.length - 1)) return (
                <ButtonGroupButton key={option.label} onClick={option.onClick} className="-ml-px rounded-r-md">{option.label}</ButtonGroupButton>
            );

            return <ButtonGroupButton key={option.label} onClick={option.onClick} className="-ml-px">{option.label}</ButtonGroupButton>;
        })}
    </span>
);

export default ButtonGroup;

const ButtonGroupButton = ({ onClick, className, children }) => (
    <button
        type="button"
        className={
            'relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 '
            + 'hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-purple-500 focus:border-purple-500 '
            + className
        }
        onClick={onClick}
    >
        {children}
    </button>
);