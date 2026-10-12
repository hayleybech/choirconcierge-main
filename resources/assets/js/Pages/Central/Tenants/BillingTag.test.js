import React from 'react';
import { render, screen } from '@testing-library/react';
import '@testing-library/jest-dom';
import BillingTag from './BillingTag';

describe('BillingTag', () => {
	it('shows a cancellation status ahead of an active trial', () => {
		render(
			<BillingTag
				billing={{
					hasGratis: false,
					onGracePeriod: true,
					ended: false,
					onTrial: true,
				}}
			/>
		);

		expect(screen.getByText('Cancelling')).toBeInTheDocument();
	});
});