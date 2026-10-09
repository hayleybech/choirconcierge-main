import React from 'react';
import {screen, render} from "@testing-library/react";
import '@testing-library/jest-dom';
import Button from "./Button";
import ButtonV2 from "./ButtonV2";

describe('Button', () => {
    it('renders a link when a url is given',  () => {
        render(<Button href="/test" />);

        expect(screen.getByRole('link')).toBeVisible();
        expect(screen.getByRole('link')).toHaveAttribute('href', '/test');
    });

    it('renders a button when an onclick is given', () => {
        render(<Button onClick={() => {}} />);

        expect(screen.getByRole('button')).toBeVisible();
    });

    it('renders v2 buttons with color, weight, and size classes', () => {
        render(<ButtonV2 color="secondary" weight="clear" theme="dark" size="lg">Test</ButtonV2>);

        expect(screen.getByRole('button')).toHaveClass('text-gray-100', 'py-2.5', 'px-5');
     		 expect(screen.getByRole('button')).not.toHaveClass('shadow-sm');
	});

	it('keeps dark theme secondary outline text light', () => {
		render(<ButtonV2 color="secondary" weight="outline" theme="dark">Test</ButtonV2>);

		expect(screen.getByRole('button')).toHaveClass('text-gray-100');
		expect(screen.getByRole('button')).not.toHaveClass('text-gray-700');
	});

	it('uses darker same-color hover styles for light clear buttons', () => {
		const {rerender} = render(<ButtonV2 color="primary" weight="clear" theme="light">Test</ButtonV2>);

		expect(screen.getByRole('button')).toHaveClass('hover:text-purple-700');

		rerender(<ButtonV2 color="secondary" weight="clear" theme="light">Test</ButtonV2>);

		expect(screen.getByRole('button')).toHaveClass('hover:text-gray-500');
	});

	it('uses darker hover text for dark secondary clear buttons', () => {
		render(<ButtonV2 color="secondary" weight="clear" theme="dark">Test</ButtonV2>);

		expect(screen.getByRole('button')).toHaveClass('text-gray-100', 'hover:text-gray-300');
	});

	it('uses darker light secondary solid styling', () => {
		render(<ButtonV2 color="secondary" weight="solid" theme="light">Test</ButtonV2>);

		expect(screen.getByRole('button')).toHaveClass('bg-gray-400', 'hover:bg-gray-500');
	});

	it('uses lighter text hover styles without a background for dark clear buttons', () => {
		render(<ButtonV2 color="danger" weight="clear" theme="dark">Test</ButtonV2>);

		expect(screen.getByRole('button')).toHaveClass('hover:text-red-300');
		expect(screen.getByRole('button')).not.toHaveClass('hover:bg-red-600/30');
	});
});