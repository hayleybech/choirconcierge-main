import React from 'react';
import { act, fireEvent, render, screen } from '@testing-library/react';
import '@testing-library/jest-dom';
import { vi } from 'vitest';
import Tooltip from './Tooltip';

describe('Tooltip', () => {
	it('shows and hides hover content', () => {
		render(
			<>
				<Tooltip content="Helpful information">
					<button type="button">Help</button>
				</Tooltip>
				<button type="button">Outside</button>
			</>
		);

		const trigger = screen.getByRole('button', { name: 'Help' });
		const wrapper = trigger.parentElement;

		fireEvent.mouseDown(trigger);
		fireEvent.focus(trigger);
		fireEvent.click(trigger);
		fireEvent.mouseUp(trigger);
		expect(screen.queryByRole('tooltip')).toBeNull();

		fireEvent.mouseEnter(wrapper);

		expect(screen.getByRole('tooltip')).toHaveTextContent('Helpful information');
		expect(trigger).toHaveAttribute('aria-describedby', screen.getByRole('tooltip').id);

		fireEvent.mouseLeave(wrapper);

		expect(screen.queryByRole('tooltip')).toBeNull();
	});

	it('supports click activation and dismisses content on outside click or Escape', () => {
		render(
			<>
				<Tooltip
					content="Reason details"
					placement="bottom-end"
					triggerProps={{ 'aria-label': 'Show reason' }}
				>
					<span>Reason icon</span>
				</Tooltip>
				<button type="button">Outside</button>
			</>
		);

		const trigger = screen.getByRole('button', { name: 'Show reason' });
		const wrapper = trigger.parentElement;
		expect(trigger).toHaveTextContent('Reason icon');

		fireEvent.mouseEnter(wrapper);
		expect(screen.getByRole('tooltip')).toHaveTextContent('Reason details');

		fireEvent.mouseLeave(wrapper);
		expect(screen.queryByRole('tooltip')).toBeNull();

		fireEvent.mouseDown(trigger);
		fireEvent.focus(trigger);
		fireEvent.click(trigger);
		fireEvent.mouseUp(trigger);

		expect(screen.getByRole('tooltip')).toHaveTextContent('Reason details');
		expect(trigger).toHaveAttribute('aria-expanded', 'true');

		fireEvent.mouseDown(screen.getByRole('button', { name: 'Outside' }));

		expect(screen.queryByRole('tooltip')).toBeNull();

		fireEvent.click(trigger);
		fireEvent.keyDown(document, { key: 'Escape' });

		expect(screen.queryByRole('tooltip')).toBeNull();
		expect(trigger).toHaveAttribute('aria-expanded', 'false');
	});

	it('does not toggle a tooltip when a button child is clicked', () => {
		const onClick = vi.fn();
		const ButtonLike = props => <button {...props} />;

		render(
			<Tooltip content="Button help">
				<ButtonLike type="button" onClick={onClick}>
					Save
				</ButtonLike>
			</Tooltip>
		);

		const trigger = screen.getByRole('button', { name: 'Save' });

		fireEvent.mouseDown(trigger);
		fireEvent.focus(trigger);
		fireEvent.click(trigger);
		fireEvent.mouseUp(trigger);

		expect(onClick).toHaveBeenCalledOnce();
		expect(screen.queryByRole('tooltip')).toBeNull();

		fireEvent.mouseEnter(trigger.parentElement);

		expect(screen.getByRole('tooltip')).toHaveTextContent('Button help');
	});

	it('shows hover content after a long touch and dismisses it on an outside tap', () => {
		vi.useFakeTimers();

		try {
			render(
				<>
					<Tooltip content="Helpful information">
						<span>Help</span>
					</Tooltip>
					<button type="button">Outside</button>
				</>
			);

			const trigger = screen.getByRole('button', { name: 'Help' });
			const wrapper = trigger.parentElement;

			fireEvent.touchStart(wrapper);
			act(() => {
				vi.advanceTimersByTime(499);
			});
			expect(screen.queryByRole('tooltip')).toBeNull();

			act(() => {
				vi.advanceTimersByTime(1);
			});
			expect(screen.getByRole('tooltip')).toHaveTextContent('Helpful information');

			fireEvent.touchEnd(wrapper);
			fireEvent.touchStart(screen.getByRole('button', { name: 'Outside' }));

			expect(screen.queryByRole('tooltip')).toBeNull();
		} finally {
			vi.useRealTimers();
		}
	});
});