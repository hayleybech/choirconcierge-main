import React from 'react';
import Icon from "./Icon";

const LayoutTopBar = ({setSidebarOpen}) => {

    return (
        <div className="relative z-10 shrink-0 flex h-16 bg-white border-b border-gray-300">
            <button
                type="button"
                className="px-4 border-r border-gray-200 text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-purple-500 xl:hidden"
                onClick={() => setSidebarOpen(true)}
            >
                <span className="sr-only">Open sidebar</span>
                <Icon icon="bars"/>
            </button>
            <div className="flex-1 pr-4 sm:px-4 flex justify-between">
                <div className="flex-1" />
            </div>
        </div>
    );
}

export default LayoutTopBar;
