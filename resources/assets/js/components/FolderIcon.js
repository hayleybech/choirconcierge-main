import React from 'react';
import resolveConfig from "tailwindcss/resolveConfig";
import tailwindConfig from "../../../../tailwind.config";
import Icon from "./Icon";

const FolderIcon = ({ icon }) => {
    const fullConfig = resolveConfig(tailwindConfig);
    const colourStyles = {
        'fa-file-word': {
            '--fa-primary-color': fullConfig.theme.colors.blue[200],
            '--fa-secondary-color': fullConfig.theme.colors.blue[500],
        },
        'fa-file-excel': {
            '--fa-primary-color': fullConfig.theme.colors.emerald[200],
            '--fa-secondary-color': fullConfig.theme.colors.emerald[500],
        },
        'fa-file-csv': {
            '--fa-primary-color': fullConfig.theme.colors.emerald[200],
            '--fa-secondary-color': fullConfig.theme.colors.emerald[500],
        },
        'fa-file-powerpoint': {
            '--fa-primary-color': fullConfig.theme.colors.amber[200],
            '--fa-secondary-color': fullConfig.theme.colors.amber[500],
        },
        'fa-file-pdf': {
            '--fa-primary-color': fullConfig.theme.colors.red[200],
            '--fa-secondary-color': fullConfig.theme.colors.red[500],
        },
        'fa-file-image': {
            '--fa-primary-color': fullConfig.theme.colors.emerald[200],
            '--fa-secondary-color': fullConfig.theme.colors.emerald[500],
        },
        'fa-file-video': {
            '--fa-primary-color': fullConfig.theme.colors.purple[200],
            '--fa-secondary-color': fullConfig.theme.colors.purple[500],
        },
        'fa-file-audio': {
            '--fa-primary-color': fullConfig.theme.colors.pink[200],
            '--fa-secondary-color': fullConfig.theme.colors.pink[500],
        },
        'fa-file': {
            '--fa-primary-color': fullConfig.theme.colors.gray[200],
            '--fa-secondary-color': fullConfig.theme.colors.gray[500],
        },
        'fa-file-alt': {
            '--fa-primary-color': fullConfig.theme.colors.gray[200],
            '--fa-secondary-color': fullConfig.theme.colors.gray[500],
        },
    };
    const style = colourStyles[icon];

    return (
        <Icon icon={icon} mr type="duotone" className="fa-swap-opacity text-lg" style={style} />
    );
}

export default FolderIcon;