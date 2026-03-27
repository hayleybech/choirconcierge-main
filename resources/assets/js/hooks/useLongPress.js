import { useCallback, useRef } from 'react';

const useLongPress = (onLongPress, onClick, { delay = 500, shouldPreventDefault = true } = {}) => {
	const timeout = useRef();
	const target = useRef();
	const isTouch = useRef(false);
	const isLongPressActive = useRef(false);

	const start = useCallback(
		event => {
			if (event.type === 'touchstart') {
				isTouch.current = true;
			}
			if (shouldPreventDefault && event.target) {
				event.target.addEventListener('touchend', preventDefault, { passive: false });
				target.current = event.target;
			}
			isLongPressActive.current = false;
			timeout.current = setTimeout(() => {
				onLongPress(event);
				isLongPressActive.current = true;
				timeout.current = null;
			}, delay);
		},
		[onLongPress, delay, shouldPreventDefault]
	);

	const clear = useCallback(
		(event, shouldTriggerClick = true) => {
			if (timeout.current) {
				clearTimeout(timeout.current);
				timeout.current = null;
			}

			if (shouldTriggerClick && !isLongPressActive.current && onClick) {
				onClick(event);
			}

			if (shouldPreventDefault && target.current) {
				target.current.removeEventListener('touchend', preventDefault);
				target.current = null;
			}
		},
		[onClick, shouldPreventDefault]
	);

	const preventDefault = event => {
		if (!event.cancelable || !isLongPressActive.current) {
			return;
		}
		event.preventDefault();
	};

	return {
		onMouseDown: e => !isTouch.current && start(e),
		onTouchStart: e => start(e),
		onMouseUp: e => !isTouch.current && clear(e),
		onMouseLeave: e => !isTouch.current && clear(e, false),
		onTouchEnd: e => {
			clear(e);
			setTimeout(() => {
				isLongPressActive.current = false;
			}, 10);
		},
		onContextMenu: e => {
			if (isTouch.current) {
				e.preventDefault();
			}
		},
	};
};

export default useLongPress;
