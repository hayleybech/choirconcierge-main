import React from 'react';

const Panel = ({ header, footer, noPadding, children }) => (
    <div className="bg-white overflow-hidden shadow rounded-lg divide-y divide-gray-200 mb-4">
        {header && <div className="px-4 py-5 sm:px-6">{header}</div>}

        <div className={noPadding ? '' : 'px-4 py-5 sm:p-6'}>{children}</div>

        {footer && <div className="px-4 py-4 sm:px-6">{footer}</div>}
    </div>
);

export default Panel;