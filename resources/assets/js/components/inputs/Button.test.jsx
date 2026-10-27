import React from 'react';
import { screen, render } from '@testing-library/react';
import '@testing-library/jest-dom';
import Button from './ButtonV2';

describe('Button', () => {
	it('renders a link when a url is given', () => {
		render(<Button href="/test" />);

		expect(screen.getByRole('link')).toBeVisible();
		expect(screen.getByRole('link')).toHaveAttribute('href', '/test');
	});

	it('renders a button when an onclick is given', () => {
		render(<Button onClick={() => {}} />);

		expect(screen.getByRole('button')).toBeVisible();
	});

	it('renders v2 buttons with intent, weight, and size classes', () => {
		render(
			<Button intent="secondary" weight="clear" theme="dark" size="lg">
				Test
			</Button>
		);

		expect(screen.getByRole('button')).toHaveClass('text-gray-100', 'py-2', 'px-4');
		expect(screen.getByRole('button')).not.toHaveClass('shadow-sm');
	});

	it('maps v2 sizes to the corresponding v1 sizes', () => {
		const { rerender } = render(<Button size="lg">Test</Button>);

		expect(screen.getByRole('button')).toHaveClass('py-2', 'px-4', 'text-md', 'rounded-md');

		rerender(<Button size="md">Test</Button>);
		expect(screen.getByRole('button')).toHaveClass('py-1.5', 'px-3', 'text-sm', 'rounded');

		rerender(<Button size="sm">Test</Button>);
		expect(screen.getByRole('button')).toHaveClass('py-1', 'px-1.5', 'text-xs', 'rounded');
	});

	it('keeps dark theme secondary outline text light', () => {
		render(
			<Button intent="secondary" weight="outline" theme="dark">
				Test
			</Button>
		);

		expect(screen.getByRole('button')).toHaveClass('text-gray-100');
	});

	it('uses a white background for light theme secondary outline buttons', () => {
		render(
			<Button intent="secondary" weight="outline" theme="light">
				Test
			</Button>
		);

		expect(screen.getByRole('button')).toHaveClass('bg-white');
	});

	it('uses darker same-color hover styles for light clear buttons', () => {
		const { rerender } = render(
			<Button intent="primary" weight="clear" theme="light">
				Test
			</Button>
		);

		expect(screen.getByRole('button')).toHaveClass('hover:text-purple-700');

		rerender(
			<Button intent="secondary" weight="clear" theme="light">
				Test
			</Button>
		);

		expect(screen.getByRole('button')).toHaveClass('hover:text-gray-600');
	});

	it('uses darker hover text for dark secondary clear buttons', () => {
		render(
			<Button intent="secondary" weight="clear" theme="dark">
				Test
			</Button>
		);

		expect(screen.getByRole('button')).toHaveClass('text-gray-100', 'hover:text-gray-200');
	});

	it('uses darker light secondary solid styling', () => {
		render(
			<Button intent="secondary" weight="solid" theme="light">
				Test
			</Button>
		);

		expect(screen.getByRole('button')).toHaveClass(
			'bg-transparent',
			'border-gray-300',
			'text-gray-700',
			'hover:bg-gray-100'
		);
		expect(screen.getByRole('button')).not.toHaveClass('bg-gray-400', 'hover:bg-gray-500');
	});

	it('uses lighter text hover styles without a background for dark clear buttons', () => {
		render(
			<Button intent="danger" weight="clear" theme="dark">
				Test
			</Button>
		);

		expect(screen.getByRole('button')).toHaveClass('hover:text-red-300');
		expect(screen.getByRole('button')).toHaveClass('hover:bg-red-600/30');
	});
});
