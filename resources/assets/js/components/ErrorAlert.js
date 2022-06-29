import React from 'react';
import Icon from "./Icon";

const ErrorAlert = ({ title, className, children }) => (
    <div className={`rounded-md bg-red-50 p-4 ${className}`}>
        <div className="flex">
            <div className="flex-shrink-0">
                <Icon icon="times-circle" className="h-5 w-5 text-red-400" aria-hidden="true" />
            </div>
            <div className="ml-3">
                <h3 className="text-sm font-bold text-red-800">{title}</h3>
                <div className="mt-2 text-sm text-red-700">
                    {children}
                </div>
            </div>
        </div>
    </div>
);


export default ErrorAlert;