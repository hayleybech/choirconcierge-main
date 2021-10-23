import React from 'react';
import {screen, render} from "@testing-library/react";
import '@testing-library/jest-dom';
import ButtonLink from "./ButtonLink";

describe('ButtonLink', () => {
    it('renders a link', () => {
        render(<ButtonLink href="/test">Test</ButtonLink>);

        expect(screen.getByRole('link')).toBeVisible();
    });
});