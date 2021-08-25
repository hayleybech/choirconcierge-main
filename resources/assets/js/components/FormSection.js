import React from 'react';

const FormSection = ({title, description, children}) => (
    <div className="pt-8">
        <div>
            <h3 className="text-lg leading-6 font-medium text-gray-900">{title}</h3>
            <p className="mt-1 text-sm text-gray-500">{description}</p>
        </div>
        <div className="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
            {children}
        </div>
    </div>
);

export default FormSection;