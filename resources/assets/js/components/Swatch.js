import React from 'react';

const Swatch = ({ colour = '#9095a0' }) => (
    <i className={"fas fa-fw fa-square mr-1.5 text-sm"} style={{ color: colour }} />
);

export default Swatch;