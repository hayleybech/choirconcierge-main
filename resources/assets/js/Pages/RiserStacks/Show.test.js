import React from 'react';
import { render, screen } from '@testing-library/react';
import { usePage } from '@inertiajs/react';
import Show from './Show';

jest.mock('@inertiajs/react', () => ({
    usePage: jest.fn(),
}));

jest.mock('../../Layouts/TenantLayout', () => ({ children }) => <div>{children}</div>);
jest.mock('../../components/PageHeader/PageHeader', () => () => null);
jest.mock('../../components/AppHead', () => () => null);
jest.mock('../../components/DateTag', () => () => null);
jest.mock('../../components/DeleteDialog', () => () => null);
jest.mock('../../components/Badge', () => ({ children }) => <span>{children}</span>);
jest.mock('../../components/RiserStack/RiserStackEditor', () => () => (
    <svg data-testid="riser-stack-editor" />
));
jest.mock('../../hooks/useRoute', () => () => ({ route: jest.fn(() => '#') }));

describe('RiserStacks/Show', () => {
    beforeEach(() => {
        usePage.mockReturnValue({ props: { tenant: {}, can: {} } });
    });

    it('places the riser stack editor in a scrollable pane', () => {
        render(
            <Show
                stack={{
                    title: 'Example stack',
                    rows: 4,
                    columns: 4,
                    front_row_length: 1,
                    front_row_on_floor: false,
                    members: [],
                    ensembles: [],
                    created_at: null,
                    updated_at: null,
                    can: {},
                }}
            />
        );

        const editorPane = screen.getByTestId('riser-stack-editor').parentElement;

        expect(editorPane.classList.contains('w-full')).toBe(true);
        expect(editorPane.classList.contains('max-w-full')).toBe(true);
        expect(editorPane.classList.contains('overflow-x-auto')).toBe(true);
    });
});
