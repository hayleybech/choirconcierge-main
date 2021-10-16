import React from 'react';
import resolveConfig from "tailwindcss/resolveConfig";
import tailwindConfig from "../../../../tailwind.config";

const FolderIcon = ({ icon }) => {
    const fullConfig = resolveConfig(tailwindConfig);
    const colourStyles = {
        'fa-file-word': {
            '--fa-primary-color': fullConfig.theme.colors.blue[200],
            '--fa-secondary-color': fullConfig.theme.colors.blue[600],
        },
        'fa-file-excel': {
            '--fa-primary-color': fullConfig.theme.colors.green[200],
            '--fa-secondary-color': fullConfig.theme.colors.green[600],
        },
        'fa-file-csv': {
            '--fa-primary-color': fullConfig.theme.colors.green[200],
            '--fa-secondary-color': fullConfig.theme.colors.green[600],
        },
        'fa-file-powerpoint': {
            '--fa-primary-color': fullConfig.theme.colors.yellow[200],
            '--fa-secondary-color': fullConfig.theme.colors.yellow[600],
        },
        'fa-file-pdf': {
            '--fa-primary-color': fullConfig.theme.colors.red[200],
            '--fa-secondary-color': fullConfig.theme.colors.red[600],
        },
        'fa-file-image': {
            '--fa-primary-color': fullConfig.theme.colors.green[200],
            '--fa-secondary-color': fullConfig.theme.colors.green[600],
        },
        'fa-file-video': {
            '--fa-primary-color': fullConfig.theme.colors.purple[200],
            '--fa-secondary-color': fullConfig.theme.colors.purple[600],
        },
        'fa-file-audio': {
            '--fa-primary-color': fullConfig.theme.colors.pink[200],
            '--fa-secondary-color': fullConfig.theme.colors.pink[600],
        },
        'fa-file': {
            '--fa-primary-color': fullConfig.theme.colors.gray[200],
            '--fa-secondary-color': fullConfig.theme.colors.gray[600],
        },
        'fa-file-alt': {
            '--fa-primary-color': fullConfig.theme.colors.gray[200],
            '--fa-secondary-color': fullConfig.theme.colors.gray[600],
        },
    };
    const style = colourStyles[icon];

    return (
        <i className={`fad fa-fw fa-swap-opacity mr-1.5 text-lg ${icon}`} style={style} />
    );
}

export default FolderIcon;