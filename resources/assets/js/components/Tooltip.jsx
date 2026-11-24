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

const Tooltip = ({ children, content, placement = 'top', trigger = 'hover', className = '' }) => {
	const [isOpen, setIsOpen] = useState(false);
	const tooltipRef = useRef(null);
	const tooltipId = useRef(null);
	const longPressTimeoutRef = useRef(null);
	const didLongPressRef = useRef(false);

	if (tooltipId.current === null) {
		tooltipId.current = `tooltip-${nextTooltipId++}`;
	}

	useEffect(() => {
		if (!isOpen) {
			return undefined;
		}

		const closeOnOutsideInteraction = event => {
			if (tooltipRef.current && !tooltipRef.current.contains(event.target)) {
				setIsOpen(false);
			}
		};
		const closeOnEscape = event => {
			if (event.key === 'Escape') {
				setIsOpen(false);
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
	const existingDescription = child?.props['aria-describedby'];
	const describedBy = isOpen
		? [existingDescription, tooltipId.current].filter(Boolean).join(' ')
		: existingDescription;
	const cancelLongPress = () => {
		window.clearTimeout(longPressTimeoutRef.current);
		longPressTimeoutRef.current = null;
	};
	const startLongPress = () => {
		if (trigger !== 'hover') {
			return;
		}

		cancelLongPress();
		didLongPressRef.current = false;
		longPressTimeoutRef.current = window.setTimeout(() => {
			didLongPressRef.current = true;
			setIsOpen(true);
			longPressTimeoutRef.current = null;
		}, 500);
	};
	const endLongPress = event => {
		cancelLongPress();
		if (didLongPressRef.current) {
			event.preventDefault();
			didLongPressRef.current = false;
		}
	};
	const triggerElement = child
		? cloneElement(child, {
				'aria-describedby': describedBy,
				...(trigger === 'click'
					? {
							'aria-expanded': isOpen,
							onClick: event => {
								child.props.onClick?.(event);
								setIsOpen(open => !open);
							},
						}
					: {}),
			})
		: children;

	return (
		<div
			ref={tooltipRef}
			className={`relative inline-flex ${className}`.trim()}
			onMouseEnter={() => trigger === 'hover' && setIsOpen(true)}
			onMouseLeave={() => trigger === 'hover' && setIsOpen(false)}
			onFocus={() => trigger === 'hover' && setIsOpen(true)}
			onTouchStart={startLongPress}
			onTouchEnd={endLongPress}
			onTouchMove={cancelLongPress}
			onTouchCancel={cancelLongPress}
			onBlur={event => {
				if (trigger === 'hover' && !event.currentTarget.contains(event.relatedTarget)) {
					setIsOpen(false);
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