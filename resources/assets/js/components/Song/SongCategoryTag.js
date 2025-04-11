import React from 'react';

const SongCategoryTag = ({ category }) => (
    <span
        key={category.id}
        className="inline-flex items-center px-2 py-0.5 rounded-sm text-xs font-medium bg-gray-200 text-gray-800"
    >
        {category.title}
    </span>
);

export default SongCategoryTag;