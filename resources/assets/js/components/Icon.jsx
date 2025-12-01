import React from 'react';
import classNames from "../classNames";

const Icon = ({ icon, type = 'solid', mr = false, ml = false, pulse = false, size = 'text-md', className = '', style = {} }) => {
    const types = {
        solid: 'fas',
        regular: 'far',
        light: 'fal',
        duotone: 'fad',
        brand: 'fab',
    };
    const typeClass = types[type];
    const iconClass = `fa-${icon.replace('fa-', '')}`;

    return (
        <i
            className={classNames(
                'fa-fw',
                size,
                typeClass,
                iconClass,
                mr ? 'mr-1.5' : '',
                ml ? 'ml-1.5' : '',
                pulse ? 'fa-pulse' : '',
                className,
            )}
            style={style}
        />
    );
}

export default Icon;