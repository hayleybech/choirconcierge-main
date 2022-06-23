import React from 'react';
import {render, screen} from "@testing-library/react";
import '@testing-library/jest-dom';
import PageHeader from "./PageHeader";

describe('PageHeader', () => {
    it('shows the page title', () => {
        render(<PageHeader title="Test" breadcrumbs={[]} />);

        expect(screen.getByText('Test')).toBeInTheDocument();
    });
});