import React, {useState} from 'react';
import {useMediaQuery} from "react-responsive";

const useFilterPane = () => {
    const isDesktop = useMediaQuery({ query: '(min-width: 1024px)' });
    const [showFilters, setShowFiltersState] = useState(() => isDesktop && (localStorage.getItem('showFilters') === 'true' ?? isDesktop));

    const setShowFilters = (value) => {
        setShowFiltersState(previousValue => {
            const nextValue = typeof value === 'function' ? value(previousValue) : value;

            localStorage.setItem('showFilters', String(nextValue));

            return nextValue;
        });
    };

    const hasNonDefaultFilters = Array.from(new URLSearchParams(location.search).keys())
        .some((key) =>
            key.includes('filter') || key.includes('sort')
        );

    let filterAction = isDesktop ? {
        label: <span>Filter<span className="inline md:hidden">/Sort</span></span>,
        icon: 'filter',
        onClick: () => setShowFilters(! showFilters),
        variant: hasNonDefaultFilters ? 'success-outline' : 'secondary',
    } : null;

    return [showFilters, setShowFilters, filterAction, hasNonDefaultFilters];
};

export default useFilterPane;