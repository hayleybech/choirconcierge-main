import React from 'react';
import Index from './Index';
import {render, screen} from "@testing-library/react";
import '@testing-library/jest-dom';

jest.mock('@inertiajs/inertia-react', () => ({
    __esModule: true,
    ...jest.requireActual('@inertiajs/inertia-react'),
    Head: ({ children }) => children,
}))

describe('Index', () => {
    it('shows the page heading', () => {
        render(<Index event={{ id: 1, title: 'A Rehearsal'}} />);

        expect(screen.getByText('Event Schedule')).toBeInTheDocument();
    })
});