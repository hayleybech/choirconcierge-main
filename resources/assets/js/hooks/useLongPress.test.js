import { render, act } from '@testing-library/react';
import React from 'react';
import useLongPress from './useLongPress';

const TestComponent = ({ onLongPress, onClick }) => {
	const props = useLongPress(onLongPress, onClick);
	return <div {...props} data-testid="target">Target</div>;
};

describe('useLongPress', () => {
	beforeEach(() => {
		jest.useFakeTimers();
	});

	afterEach(() => {
		jest.useRealTimers();
	});

	it('should NOT call preventDefault on short press', () => {
		const onLongPress = jest.fn();
		const onClick = jest.fn();
		const { getByTestId } = render(<TestComponent onLongPress={onLongPress} onClick={onClick} />);
		const target = getByTestId('target');

		const preventDefault = jest.fn();
		
		// fireEvent doesn't easily allow mocking preventDefault on the event object that reaches the DOM listener
		// so we'll manually dispatch
		const touchStartEvent = new Event('touchstart', { bubbles: true, cancelable: true });
		target.dispatchEvent(touchStartEvent);

		const touchEndEvent = new Event('touchend', { bubbles: true, cancelable: true });
		touchEndEvent.preventDefault = preventDefault;

		act(() => {
			target.dispatchEvent(touchEndEvent);
		});

		expect(onLongPress).not.toHaveBeenCalled();
		expect(onClick).toHaveBeenCalled();
		expect(preventDefault).not.toHaveBeenCalled();
	});

	it('should call preventDefault on long press if it was triggered', () => {
		const onLongPress = jest.fn();
		const onClick = jest.fn();
		const { getByTestId } = render(<TestComponent onLongPress={onLongPress} onClick={onClick} />);
		const target = getByTestId('target');

		const preventDefault = jest.fn();
		
		const touchStartEvent = new Event('touchstart', { bubbles: true, cancelable: true });
		target.dispatchEvent(touchStartEvent);

		act(() => {
			jest.advanceTimersByTime(600);
		});

		expect(onLongPress).toHaveBeenCalled();

		const touchEndEvent = new Event('touchend', { bubbles: true, cancelable: true });
		touchEndEvent.preventDefault = preventDefault;

		act(() => {
			target.dispatchEvent(touchEndEvent);
		});

		expect(preventDefault).toHaveBeenCalled();
	});
});
