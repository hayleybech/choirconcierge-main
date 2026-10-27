import React from 'react';
import { render } from '@testing-library/react';
import TenantNotice from './TenantNotice';

describe('TenantNotice', () => {
	it('does not add layout spacing to notices', () => {
		const { container } = render(<TenantNotice variant="info">Inline notice</TenantNotice>);

		expect(container.firstChild.classList.contains('mt-[47px]')).toBe(false);
	});
});
