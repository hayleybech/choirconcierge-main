import React from 'react';
import Icon from "./Icon";

const FolderIcon = ({ icon }) => {
    const colourStyles = {
        'fa-file-word': {
            '--fa-primary-color': 'var(--color-blue-200)',
            '--fa-secondary-color': 'var(--color-blue-500)',
        },
        'fa-file-excel': {
            '--fa-primary-color': 'var(--color-emerald-200)',
            '--fa-secondary-color': 'var(--color-emerald-500)',
        },
        'fa-file-csv': {
            '--fa-primary-color': 'var(--color-emerald-200)',
            '--fa-secondary-color': 'var(--color-emerald-500)',
        },
        'fa-file-powerpoint': {
            '--fa-primary-color': 'var(--color-amber-200)',
            '--fa-secondary-color': 'var(--color-amber-500)',
        },
        'fa-file-pdf': {
            '--fa-primary-color': 'var(--color-red-200)',
            '--fa-secondary-color': 'var(--color-red-500)',
        },
        'fa-file-image': {
            '--fa-primary-color': 'var(--color-emerald-200)',
            '--fa-secondary-color': 'var(--color-emerald-500)',
        },
        'fa-file-video': {
            '--fa-primary-color': 'var(--color-purple-200)',
            '--fa-secondary-color': 'var(--color-purple-500)',
        },
        'fa-file-audio': {
            '--fa-primary-color': 'var(--color-pink-200)',
            '--fa-secondary-color': 'var(--color-pink-500)',
        },
        'fa-file': {
            '--fa-primary-color': 'var(--color-gray-200)',
            '--fa-secondary-color': 'var(--color-gray-500)',
        },
        'fa-file-alt': {
            '--fa-primary-color': 'var(--color-gray-200)',
            '--fa-secondary-color': 'var(--color-gray-500)',
        },
    };
    const style = colourStyles[icon];

    return (
        <Icon icon={icon} mr type="duotone" className="fa-swap-opacity text-lg" style={style} />
    );
}

export default FolderIcon;