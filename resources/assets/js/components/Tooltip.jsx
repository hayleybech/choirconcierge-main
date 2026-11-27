import React, { cloneElement, isValidElement, useEffect, useRef, useState } from 'react';

const placements = {
	top: 'bottom-full left-1/2 mb-2 -translate-x-1/2',
	'top-start': 'bottom-full left-0 mb-2',
	'top-end': 'bottom-full right-0 mb-2',
	bottom: 'left-1/2 top-full mt-2 -translate-x-1/2',
	'bottom-start': 'left-0 top-full mt-2',
	'bottom-end': 'right-0 top-5',
	left: 'right-full top-1/2 mr-2 -translate-y-1/2',
	right: 'left-full top-1/2 ml-2 -translate-y-1/2',
};

let nextTooltipId = 0;

const Tooltip = ({ children, content, placement = 'top', className = '', triggerProps = {} }) => {
	const [isHovered, setIsHovered] = useState(false);
	const [isFocused, setIsFocused] = useState(false);
	const [isClickOpen, setIsClickOpen] = useState(false);
	const [isLongPressOpen, setIsLongPressOpen] = useState(false);
	const [isDismissed, setIsDismissed] = useState(false);
	const isOpen = !isDismissed && (isHovered || isFocused || isClickOpen || isLongPressOpen);
	const tooltipRef = useRef(null);
	const tooltipId = useRef(null);
	const longPressTimeoutRef = useRef(null);
	const didLongPressRef = useRef(false);
	const isPointerInteractionRef = useRef(false);

	if (tooltipId.current === null) {
		tooltipId.current = `tooltip-${nextTooltipId++}`;
	}

	useEffect(() => {
		if (!isOpen) {
			return undefined;
		}

		const closeOnOutsideInteraction = event => {
			if (tooltipRef.current && !tooltipRef.current.contains(event.target)) {
				setIsHovered(false);
				setIsFocused(false);
				setIsClickOpen(false);
				setIsLongPressOpen(false);
				setIsDismissed(true);
			}
		};
		const closeOnEscape = event => {
			if (event.key === 'Escape') {
				setIsHovered(false);
				setIsFocused(false);
				setIsClickOpen(false);
				setIsLongPressOpen(false);
				setIsDismissed(true);
			}
		};

		document.addEventListener('mousedown', closeOnOutsideInteraction);
		document.addEventListener('touchstart', closeOnOutsideInteraction);
		document.addEventListener('keydown', closeOnEscape);

		return () => {
			document.removeEventListener('mousedown', closeOnOutsideInteraction);
			document.removeEventListener('touchstart', closeOnOutsideInteraction);
			document.removeEventListener('keydown', closeOnEscape);
		};
	}, [isOpen]);

	useEffect(
		() => () => {
			window.clearTimeout(longPressTimeoutRef.current);
		},
		[]
	);

	if (content === null || content === undefined || content === '') {
		return children;
	}

	const child = isValidElement(children) ? children : null;
	const isButtonChild = child?.type === 'button' || child?.props.type === 'button';
	const {
		'aria-describedby': triggerDescription,
		className: triggerClassName = '',
		onClick: onTriggerClick,
		...additionalTriggerProps
	} = triggerProps;
	const existingDescription = [child?.props['aria-describedby'], triggerDescription].filter(Boolean).join(' ') || undefined;
	const describedBy = isOpen
		? [existingDescription, tooltipId.current].filter(Boolean).join(' ')
		: existingDescription;
	const cancelLongPress = () => {
		window.clearTimeout(longPressTimeoutRef.current);
		longPressTimeoutRef.current = null;
	};
	const startLongPress = () => {
		isPointerInteractionRef.current = true;
		cancelLongPress();
		didLongPressRef.current = false;
		longPressTimeoutRef.current = window.setTimeout(() => {
			didLongPressRef.current = true;
			setIsLongPressOpen(true);
			setIsDismissed(false);
			longPressTimeoutRef.current = null;
		}, 500);
	};
	const endLongPress = event => {
		cancelLongPress();
		isPointerInteractionRef.current = false;
		if (didLongPressRef.current) {
			event.preventDefault();
			didLongPressRef.current = false;
		}
	};
	const triggerElement = !isButtonChild ? (
		<button
			{...additionalTriggerProps}
			type="button"
			className={triggerClassName}
			aria-describedby={describedBy}
			aria-expanded={isOpen}
			onClick={event => {
				onTriggerClick?.(event);
				if (isClickOpen) {
					setIsHovered(false);
					setIsFocused(false);
					setIsClickOpen(false);
					setIsLongPressOpen(false);
					setIsDismissed(true);
					return;
				}

				setIsClickOpen(true);
				setIsDismissed(false);
			}}
		>
			{children}
		</button>
	) : (
		cloneElement(child, {
			'aria-describedby': describedBy,
		})
	);

	return (
		<div
			ref={tooltipRef}
			className={`relative inline-flex ${className}`.trim()}
			onMouseDown={() => {
				isPointerInteractionRef.current = true;
			}}
			onMouseUp={() => {
				isPointerInteractionRef.current = false;
			}}
			onMouseEnter={() => {
				setIsHovered(true);
				setIsDismissed(false);
			}}
			onMouseLeave={() => setIsHovered(false)}
			onFocus={() => {
				if (!isPointerInteractionRef.current) {
					setIsFocused(true);
					setIsDismissed(false);
				}
			}}
			onTouchStart={startLongPress}
			onTouchEnd={endLongPress}
			onTouchMove={cancelLongPress}
			onTouchCancel={() => {
				cancelLongPress();
				isPointerInteractionRef.current = false;
			}}
			onBlur={event => {
				if (!event.currentTarget.contains(event.relatedTarget)) {
					isPointerInteractionRef.current = false;
					setIsFocused(false);
				}
			}}
		>
			{triggerElement}
			{isOpen && (
				<span
					id={tooltipId.current}
					role="tooltip"
					className={`absolute z-20 w-max max-w-xs whitespace-normal break-words rounded bg-gray-800 px-2 py-1.5 text-left text-[11px] font-normal leading-tight text-white shadow-lg ${placements[placement] ?? placements.top}`}
				>
					{content}
				</span>
			)}
		</div>
	);
};

export default Tooltip;