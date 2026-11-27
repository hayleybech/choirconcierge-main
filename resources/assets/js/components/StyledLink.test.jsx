import React from 'react';
import { render, screen } from '@testing-library/react';
import '@testing-library/jest-dom';
import StyledLink from './StyledLink';

it('renders a link to the supplied URL', () => {
	render(<StyledLink href="/test">Default link</StyledLink>);

	expect(screen.getByRole('link', { name: 'Default link' })).toHaveAttribute('href', '/test');
});
