import React, {useState} from 'react';
import {useMediaQuery} from "react-responsive";

const useFilterPane = () => {
    const isDesktop = useMediaQuery({ query: '(min-width: 1024px)' });
    const [showFilters, setShowFiltersState] = useState(() => localStorage.getItem('showFilters') === 'true' ?? isDesktop);

    const setShowFilters = (value) => {
        setShowFiltersState(value);
        localStorage.setItem('showFilters', value);
    };

    const hasNonDefaultFilters = Array.from(new URLSearchParams(location.search).keys())
        .some((key) =>
            key.includes('filter') || key.includes('sort')
        );

    let filterAction = {
        label: <span>Filter<span className="inline md:hidden">/Sort</span></span>,
        icon: 'filter',
        onClick: () => setShowFilters(! showFilters),
        variant: hasNonDefaultFilters ? 'success-solid' : 'secondary',
    };

    return [showFilters, setShowFilters, filterAction, hasNonDefaultFilters];
};

export default useFilterPane;