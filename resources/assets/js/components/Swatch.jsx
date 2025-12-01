import React from 'react';
import Icon from "./Icon";

const Swatch = ({ colour = 'gray' }) => (
    <Icon icon="square" mr  className={`text-sm text-${colour}-500`} />
);

export default Swatch;