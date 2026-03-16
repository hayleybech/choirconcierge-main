import React from 'react';

const Prose = ({ className = '', content }) => (
    <div className={`prose [&>li]:mb-0 ${className}`} dangerouslySetInnerHTML={{ __html: content }} />
);

export default Prose;