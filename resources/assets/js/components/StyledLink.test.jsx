import React from 'react';
import { render, screen } from '@testing-library/react';
import '@testing-library/jest-dom';
import StyledLink from './StyledLink';

it('defaults to no visual intent', () => {
	render(<StyledLink href="/test">Default link</StyledLink>);

	expect(screen.getByRole('link', { name: 'Default link' })).toHaveClass('font-medium');
	expect(screen.getByRole('link', { name: 'Default link' })).not.toHaveClass('text-purple-700', 'underline');
});

it('styles primary and secondary link intents', () => {
	render(
		<>
			<StyledLink href="/primary" intent="primary">Primary link</StyledLink>
			<StyledLink href="/secondary" intent="secondary">Secondary link</StyledLink>
		</>
	);

	expect(screen.getByRole('link', { name: 'Primary link' })).toHaveClass(
		'text-purple-700',
		'no-underline',
		'hover:text-sky-500'
	);
	expect(screen.getByRole('link', { name: 'Secondary link' })).toHaveClass(
		'text-purple-700',
		'underline',
		'hover:no-underline'
	);
});
