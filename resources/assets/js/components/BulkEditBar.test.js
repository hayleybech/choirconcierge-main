import React from 'react';
import { render } from '@testing-library/react';
import BulkEditBar from './BulkEditBar';

describe('BulkEditBar', () => {
	it('sizes itself to fit its actions', () => {
		const { container } = render(
			<BulkEditBar
				bulkEdit={{
					isAllowed: true,
					selectedIds: [1],
					noun: 'singer',
					clearSelections: jest.fn(),
					setIsForcedMobile: jest.fn(),
					canUpdate: false,
					canDelete: false,
				}}
				actions={<button>Going</button>}
			/>
		);

		expect(container.firstChild.className).toContain('w-max');
	});
});