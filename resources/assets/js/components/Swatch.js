import React from 'react';
import Icon from "./Icon";

const Swatch = ({ colour = '#9095a0' }) => (
    <Icon icon="square" mr style={{ color: colour }} className="text-sm" />
);

export default Swatch;