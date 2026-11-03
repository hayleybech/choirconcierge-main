import React from 'react';
import { fireEvent, render, screen } from '@testing-library/react';
import { vi } from 'vitest';
import useSortFilterForm from './useSortFilterForm';

const mocks = vi.hoisted(() => ({
	get: vi.fn(),
	setData: vi.fn(),
	transform: vi.fn(),
	route: vi.fn(routeName => `/${routeName}`),
	initialData: vi.fn(),
}));

vi.mock('@inertiajs/react', () => ({
	useForm: initialData => {
		mocks.initialData(initialData);

		return {
			data: {},
			setData: mocks.setData,
			get: mocks.get,
			transform: mocks.transform,
		};
	},
}));

vi.mock('./useRoute', () => ({
	default: () => ({ route: mocks.route }),
}));

const SubmitControl = () => {
	const form = useSortFilterForm('singers.index', [], [{ id: 'full-name', default: true }]);

	return (
		<button type="button" onClick={() => form.submit(undefined, { preserveState: true })}>
			Submit
		</button>
	);
};

const FilterControl = () => {
	useSortFilterForm('singers.index', [{ name: 'fee_status', multiple: true, defaultValue: [] }], [
		{ id: 'full-name', default: true },
	]);

	return null;
};

const DateRangeFilterControl = () => {
	useSortFilterForm(
		'singers.attendance',
		[
			{
				name: 'date_range',
				start_name: 'starts_after',
				end_name: 'starts_before',
				type: 'daterange',
			},
		],
		[{ id: 'start-date', default: true }]
	);

	return null;
};

it('passes preserveState through to the Inertia visit', () => {
	render(<SubmitControl />);

	fireEvent.click(screen.getByRole('button', { name: 'Submit' }));

	expect(mocks.get).toHaveBeenCalledWith('/singers.index', { preserveState: true });
});

it('normalizes a legacy scalar value for a multi-value filter', () => {
	window.history.replaceState({}, '', '/?filter%5Bfee_status%5D=expired');
	render(<FilterControl />);

	expect(mocks.initialData).toHaveBeenLastCalledWith({
		sort: 'full-name',
		sortDir: 'asc',
		fee_status: ['expired'],
	});
	window.history.replaceState({}, '', '/');
});

it('hydrates both fields from a date-range filter in the query string', () => {
	window.history.replaceState(
		{},
		'',
		'/?filter%5Bstarts_after%5D=2026-04-10&filter%5Bstarts_before%5D=2026-04-12'
	);
	const expectedData = {
		sort: 'start-date',
		sortDir: 'asc',
		starts_after: '2026-04-10',
		starts_before: '2026-04-12',
	};

	render(<DateRangeFilterControl />);

	expect(mocks.initialData).toHaveBeenLastCalledWith(expectedData);
	window.history.replaceState({}, '', '/');
});