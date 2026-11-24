import React from 'react';
import { fireEvent, render, screen } from '@testing-library/react';
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

		fireEvent.mouseEnter(wrapper);

		expect(screen.getByRole('tooltip')).toHaveTextContent('Helpful information');
		expect(trigger).toHaveAttribute('aria-describedby', screen.getByRole('tooltip').id);

		fireEvent.mouseLeave(wrapper);

		expect(screen.queryByRole('tooltip')).toBeNull();
	});

	it('toggles click content and dismisses it on outside click or Escape', () => {
		render(
			<>
				<Tooltip content="Reason details" trigger="click" placement="bottom-end">
					<button type="button">Show reason</button>
				</Tooltip>
				<button type="button">Outside</button>
			</>
		);

		const trigger = screen.getByRole('button', { name: 'Show reason' });

		fireEvent.click(trigger);

		expect(screen.getByRole('tooltip')).toHaveTextContent('Reason details');
		expect(trigger).toHaveAttribute('aria-expanded', 'true');

		fireEvent.mouseDown(screen.getByRole('button', { name: 'Outside' }));

		expect(screen.queryByRole('tooltip')).toBeNull();

		fireEvent.click(trigger);
		fireEvent.keyDown(document, { key: 'Escape' });

		expect(screen.queryByRole('tooltip')).toBeNull();
		expect(trigger).toHaveAttribute('aria-expanded', 'false');
	});
});