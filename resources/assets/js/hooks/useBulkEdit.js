import { useEffect, useRef, useState } from 'react';
import { useMediaQuery } from 'react-responsive';

const useBulkEdit = (items = [], canUpdate = false) => {
	const [selectedIds, setSelectedIds] = useState([]);
	const [showModal, setShowModal] = useState(false);
	const [isSelectionModeMobile, setIsSelectionModeMobile] = useState(false);
	const [lastSelectedId, setLastSelectedId] = useState(null);
	const refSelectAll = useRef(null);

	const isDesktop = useMediaQuery({ query: '(min-width: 1024px)' });

	useEffect(() => {
		if (!refSelectAll.current) {
			return;
		}

		refSelectAll.current.indeterminate = selectedIds.length > 0 && selectedIds.length < items.length;
	}, [selectedIds, items.length]);

	const toggleSelection = id => {
		setSelectedIds(prev => (prev.includes(id) ? prev.filter(selectedId => selectedId !== id) : [...prev, id]));
		setLastSelectedId(id);
	};

	const handleRowClick = (id, event) => {
		if (event.ctrlKey || event.metaKey) {
			toggleSelection(id);
			return;
		}

		if (event.shiftKey && lastSelectedId !== null) {
			const currentIndex = items.findIndex(item => item.id === id);
			const lastIndex = items.findIndex(item => item.id === lastSelectedId);

			if (currentIndex !== -1 && lastIndex !== -1) {
				const start = Math.min(currentIndex, lastIndex);
				const end = Math.max(currentIndex, lastIndex);
				const rangeIds = items.slice(start, end + 1).map(item => item.id);

				setSelectedIds(prev => [...new Set([...prev, ...rangeIds])]);
				setLastSelectedId(id);
				return;
			}
		}

		setSelectedIds([id]);
		setLastSelectedId(id);
	};

	const toggleAll = () => {
		if (selectedIds.length === items.length) {
			setSelectedIds([]);
		} else {
			setSelectedIds(items.map(item => item.id));
		}
	};

	const clearSelections = () => setSelectedIds([]);

	const toggleSelectionModeMobile = () => setIsSelectionModeMobile(prev => !prev);

	const action = canUpdate
		? isDesktop
			? {
					label: `Edit ${selectedIds.length > 0 ? `(${selectedIds.length})` : ''}`,
					icon: 'pencil',
					onClick: () => setShowModal(true),
					variant: 'secondary',
					disabled: selectedIds.length === 0,
			  }
			: {
					label: isSelectionModeMobile ? 'Cancel Selection' : 'Select Multiple',
					icon: 'check-square',
					onClick: () => toggleSelectionModeMobile(),
					variant: 'secondary',
			  }
		: null;

	return {
		selectedIds,
		setSelectedIds,
		showModal,
		setShowModal,
		refSelectAll,
		isSelectionModeMobile,
		setIsSelectionModeMobile,
		toggleSelection,
		handleRowClick,
		toggleAll,
		clearSelections,
		toggleSelectionModeMobile,
		action,
		canUpdate,
	};
};

export default useBulkEdit;
