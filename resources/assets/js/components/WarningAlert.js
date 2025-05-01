import React from 'react';
import Icon from "./Icon";

const WarningAlert = ({ title, className, children }) => (
    <div className={`rounded-md bg-yellow-50 p-4 ${className}`}>
        <div className="flex">
            <div className="flex-shrink-0">
                <Icon icon="exclamation-triangle" className="h-5 w-5 text-yellow-400" aria-hidden="true" />
            </div>
            <div className="ml-3">
                <h3 className="text-sm font-bold text-yellow-800">{title}</h3>
                <div className="mt-2 text-sm text-yellow-700">
                    {children}
                </div>
            </div>
        </div>
    </div>
);


export default WarningAlert;