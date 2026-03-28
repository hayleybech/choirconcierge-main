import { useEffect, useRef, useState, MutableRefObject, Dispatch, SetStateAction } from 'react';
import { useMediaQuery } from 'react-responsive';
import { plural } from '../util';

interface Item {
	id: number | string;
	[key: string]: any;
}

interface Action {
	label: string;
	icon: string;
	onClick: () => void;
	variant: string;
}

export interface UseBulkEditReturn {
	selectedIds: (number | string)[];
	setSelectedIds: Dispatch<SetStateAction<(number | string)[]>>;
	showEditModal: boolean;
	setShowEditModal: Dispatch<SetStateAction<boolean>>;
	showDeleteModal: boolean;
	setShowDeleteModal: Dispatch<SetStateAction<boolean>>;
	refSelectAll: MutableRefObject<HTMLInputElement | null>;
	setIsForcedMobile: Dispatch<SetStateAction<boolean>>;
	isActiveMobile: boolean;
	toggleSelection: (id: number | string) => void;
	handleRowClick: (id: number | string, event: React.MouseEvent | React.KeyboardEvent | any) => void;
	toggleAll: () => void;
	clearSelections: () => void;
	action: Action | false;
	canUpdate: boolean;
	canDelete: boolean;
	totalItems: number;
	isAllowed: boolean;
	noun: string;
	hideEdit: boolean;
}

const useBulkEdit = (
	items: Item[] = [],
	canUpdate: boolean = false,
	canDelete: boolean = false,
	noun: string = 'Item',
	hideEdit: boolean = false
): UseBulkEditReturn => {
	const [selectedIds, setSelectedIds] = useState<(number | string)[]>([]);
	const [showEditModal, setShowEditModal] = useState<boolean>(false);
	const [showDeleteModal, setShowDeleteModal] = useState<boolean>(false);
	const [isForcedMobile, setIsForcedMobile] = useState<boolean>(false);
	const [lastSelectedId, setLastSelectedId] = useState<number | string | null>(null);
	const refSelectAll = useRef<HTMLInputElement | null>(null);

	const isDesktop = useMediaQuery({ query: '(min-width: 1024px)' });

	useEffect(() => {
		if (!refSelectAll.current) {
			return;
		}

		refSelectAll.current.indeterminate = selectedIds.length > 0 && selectedIds.length < items.length;
	}, [selectedIds, items.length]);

	const toggleSelection = (id: number | string): void => {
		setSelectedIds(prev => (prev.includes(id) ? prev.filter(selectedId => selectedId !== id) : [...prev, id]));
		setLastSelectedId(id);
	};

	const handleRowClick = (id: number | string, event: React.MouseEvent | any): void => {
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

	const toggleAll = (): void => {
		if (selectedIds.length === items.length) {
			setSelectedIds([]);
		} else {
			setSelectedIds(items.map(item => item.id));
		}
	};

	const clearSelections = (): void => setSelectedIds([]);

	const isActiveMobile = isForcedMobile || selectedIds.length > 0;

	const isAllowed = canUpdate || canDelete;

	const action: Action | false = (isAllowed &&
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
		}) as Action | false;

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
		hideEdit,
	};
};

export default useBulkEdit;
