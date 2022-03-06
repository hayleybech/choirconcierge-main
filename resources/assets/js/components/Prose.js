import React from 'react';

const Prose = ({ className, content }) => (
    <div className={`prose ${className}`} dangerouslySetInnerHTML={{ __html: content }} />
);

export default Prose;