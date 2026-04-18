import { useCallback, useRef } from 'react';

const useLongPress = (onLongPress, onClick, { delay = 500, shouldPreventDefault = true } = {}) => {
	const timeout = useRef();
	const target = useRef();
	const isTouch = useRef(false);
	const isLongPressActive = useRef(false);
	const startPos = useRef({ x: 0, y: 0 });

	const start = useCallback(
		event => {
			if (event.type === 'touchstart') {
				isTouch.current = true;
				if (event.touches && event.touches[0]) {
					startPos.current = {
						x: event.touches[0].clientX,
						y: event.touches[0].clientY,
					};
				}
			} else {
				startPos.current = {
					x: event.clientX,
					y: event.clientY,
				};
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

	const move = useCallback(
		event => {
			if (!timeout.current) {
				return;
			}

			const x = event.type === 'touchmove' ? (event.touches && event.touches[0] ? event.touches[0].clientX : 0) : event.clientX;
			const y = event.type === 'touchmove' ? (event.touches && event.touches[0] ? event.touches[0].clientY : 0) : event.clientY;

			if (event.type === 'touchmove' && (!event.touches || !event.touches[0])) {
				return;
			}

			const distance = Math.sqrt(Math.pow(x - startPos.current.x, 2) + Math.pow(y - startPos.current.y, 2));

			if (distance > 10) {
				clearTimeout(timeout.current);
				timeout.current = null;
			}
		},
		[]
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

	const onMouseDown = useCallback(
		e => {
			isTouch.current = false;
			start(e);
		},
		[start]
	);

	const onMouseMove = useCallback(e => !isTouch.current && move(e), [move]);

	const onTouchStart = useCallback(e => start(e), [start]);

	const onTouchMove = useCallback(e => move(e), [move]);

	const onMouseUp = useCallback(e => !isTouch.current && clear(e), [clear]);

	const onMouseLeave = useCallback(e => !isTouch.current && clear(e, false), [clear]);

	const onTouchEnd = useCallback(
		e => {
			clear(e);
			setTimeout(() => {
				isLongPressActive.current = false;
			}, 10);
		},
		[clear]
	);

	const onContextMenu = useCallback(e => {
		if (isTouch.current) {
			e.preventDefault();
		}
	}, []);

	const preventDefault = event => {
		if (!event.cancelable || !isLongPressActive.current) {
			return;
		}
		event.preventDefault();
	};

	return {
		onMouseDown,
		onMouseMove,
		onTouchStart,
		onTouchMove,
		onMouseUp,
		onMouseLeave,
		onTouchEnd,
		onContextMenu,
	};
};

export default useLongPress;
