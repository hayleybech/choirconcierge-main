import React from 'react';
import { render } from '@testing-library/react';
import TenantNotice from './TenantNotice';

describe('TenantNotice', () => {
	it('offsets global notices below the mobile page top bar', () => {
		const { container } = render(
			<TenantNotice variant="info" global>
				Billing notice
			</TenantNotice>
		);

		expect(container.firstChild.classList.contains('mt-[47px]')).toBe(true);
		expect(container.firstChild.classList.contains('xl:mt-0')).toBe(true);
	});

	it('does not offset inline notices', () => {
		const { container } = render(<TenantNotice variant="info">Inline notice</TenantNotice>);

		expect(container.firstChild.classList.contains('mt-[47px]')).toBe(false);
	});
});
