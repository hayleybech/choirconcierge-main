import { useEffect, useRef, useState } from 'react';

const useBulkEdit = (items = []) => {
	const [selectedIds, setSelectedIds] = useState([]);
	const [showModal, setShowModal] = useState(false);
	const [isSelectionModeMobile, setIsSelectionModeMobile] = useState(false);
	const refSelectAll = useRef(null);

	useEffect(() => {
		if (!refSelectAll.current) {
			return;
		}

		refSelectAll.current.indeterminate = selectedIds.length > 0 && selectedIds.length < items.length;
	}, [selectedIds, items.length]);

	const toggleSelection = id =>
		setSelectedIds(prev => (prev.includes(id) ? prev.filter(selectedId => selectedId !== id) : [...prev, id]));

	const toggleAll = () => {
		if (selectedIds.length === items.length) {
			setSelectedIds([]);
		} else {
			setSelectedIds(items.map(item => item.id));
		}
	};

	const clearSelections = () => setSelectedIds([]);

	const toggleSelectionModeMobile = () => setIsSelectionModeMobile(prev => !prev);

	const actions = [
		{
			label: `Edit ${selectedIds.length > 0 ? `(${selectedIds.length})` : ''}`,
			icon: 'pencil',
			onClick: () => setShowModal(true),
			variant: 'secondary',
			disabled: selectedIds.length === 0,
			hideOnMobile: true,
		},
		{
			label: isSelectionModeMobile ? 'Cancel Selection' : 'Select Multiple',
			icon: 'check-square',
			onClick: () => toggleSelectionModeMobile(),
			variant: 'secondary',
			hideOnDesktop: true,
		},
	];

	return {
		selectedIds,
		setSelectedIds,
		showModal,
		setShowModal,
		refSelectAll,
		isSelectionModeMobile,
		setIsSelectionModeMobile,
		toggleSelection,
		toggleAll,
		clearSelections,
		toggleSelectionModeMobile,
		actions,
	};
};

export default useBulkEdit;
