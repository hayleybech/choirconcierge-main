import React, {useState} from 'react';

const useFilterPane = () => {
    const [showFilters, setShowFilters] = useState(hasNonDefaultFilters());

    return [showFilters, setShowFilters];
};

export default useFilterPane;

const hasNonDefaultFilters = () => {
    return Array.from(new URLSearchParams(location.search).keys())
        .some((key) =>
            key.includes('filter') || key.includes('sort')
        );
}