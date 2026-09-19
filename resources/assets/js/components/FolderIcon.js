import React from 'react';
const TAILWIND_COLOR = color => `var(--color-${color})`;
import Icon from "./Icon";

const FolderIcon = ({ icon }) => {
    const colourStyles = {
        'fa-file-word': {
            '--fa-primary-color': TAILWIND_COLOR('blue-200'),
            '--fa-secondary-color': TAILWIND_COLOR('blue-500'),
        },
        'fa-file-excel': {
            '--fa-primary-color': TAILWIND_COLOR('emerald-200'),
            '--fa-secondary-color': TAILWIND_COLOR('emerald-500'),
        },
        'fa-file-csv': {
            '--fa-primary-color': TAILWIND_COLOR('emerald-200'),
            '--fa-secondary-color': TAILWIND_COLOR('emerald-500'),
        },
        'fa-file-powerpoint': {
            '--fa-primary-color': TAILWIND_COLOR('amber-200'),
            '--fa-secondary-color': TAILWIND_COLOR('amber-500'),
        },
        'fa-file-pdf': {
            '--fa-primary-color': TAILWIND_COLOR('red-200'),
            '--fa-secondary-color': TAILWIND_COLOR('red-500'),
        },
        'fa-file-image': {
            '--fa-primary-color': TAILWIND_COLOR('emerald-200'),
            '--fa-secondary-color': TAILWIND_COLOR('emerald-500'),
        },
        'fa-file-video': {
            '--fa-primary-color': TAILWIND_COLOR('purple-200'),
            '--fa-secondary-color': TAILWIND_COLOR('purple-500'),
        },
        'fa-file-audio': {
            '--fa-primary-color': TAILWIND_COLOR('pink-200'),
            '--fa-secondary-color': TAILWIND_COLOR('pink-500'),
        },
        'fa-file': {
            '--fa-primary-color': TAILWIND_COLOR('gray-200'),
            '--fa-secondary-color': TAILWIND_COLOR('gray-500'),
        },
        'fa-file-alt': {
            '--fa-primary-color': TAILWIND_COLOR('gray-200'),
            '--fa-secondary-color': TAILWIND_COLOR('gray-500'),
        },
    };
    const style = colourStyles[icon];

    return (
        <Icon icon={icon} mr type="duotone" className="fa-swap-opacity text-lg" style={style} />
    );
}

export default FolderIcon;
