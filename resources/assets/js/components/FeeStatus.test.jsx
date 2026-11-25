import React from 'react';
import { fireEvent, render, screen } from '@testing-library/react';
import '@testing-library/jest-dom';
import FeeStatus from './FeeStatus';

describe('FeeStatus', () => {
	it('does not show a tooltip for an unknown fee status', () => {
		render(<FeeStatus status="unknown" tooltip="No fee status is available" />);

		fireEvent.mouseEnter(screen.getByText('Unknown').parentElement);

		expect(screen.queryByRole('tooltip')).toBeNull();
	});

	it('shows the tooltip for a known fee status', () => {
		render(<FeeStatus status="paid" tooltip="Fee is current" />);

		fireEvent.mouseEnter(screen.getByText('Paid').parentElement.parentElement);

		expect(screen.getByRole('tooltip')).toHaveTextContent('Fee is current');
	});
});