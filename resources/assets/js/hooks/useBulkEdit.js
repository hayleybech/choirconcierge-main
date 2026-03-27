import { useEffect, useRef, useState } from 'react';
import { useMediaQuery } from 'react-responsive';
import { plural } from '../util';

const useBulkEdit = (items = [], canUpdate = false, canDelete = false, noun = 'Item') => {
	const [selectedIds, setSelectedIds] = useState([]);
	const [showEditModal, setShowEditModal] = useState(false);
	const [showDeleteModal, setShowDeleteModal] = useState(false);
	const [isForcedMobile, setIsForcedMobile] = useState(false);
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

	const isActiveMobile = isForcedMobile || selectedIds.length > 0;

	const isAllowed = canUpdate || canDelete;

	const action = isAllowed &&
		!isDesktop && {
			label: isActiveMobile ? 'Cancel Selection' : `Select ${plural(noun)}`,
			icon: 'check-square',
			onClick: () => {
				if (isActiveMobile) {
					setIsForcedMobile(false);
					setSelectedIds([]);
				} else {
					setIsForcedMobile(true);
				}
			},
			variant: 'secondary',
		};

	return {
		selectedIds,
		setSelectedIds,
		showEditModal,
		setShowEditModal,
		showDeleteModal,
		setShowDeleteModal,
		refSelectAll,
		setIsForcedMobile,
		isActiveMobile,
		toggleSelection,
		handleRowClick,
		toggleAll,
		clearSelections,
		action,
		canUpdate,
		canDelete,
		totalItems: items.length,
		isAllowed,
		noun,
	};
};

export default useBulkEdit;
