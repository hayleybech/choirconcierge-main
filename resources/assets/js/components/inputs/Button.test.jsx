import React from 'react';
import {screen, render} from "@testing-library/react";
import '@testing-library/jest-dom';
import Button from "./Button";

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
});