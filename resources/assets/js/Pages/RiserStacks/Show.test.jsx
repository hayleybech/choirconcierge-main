import React from 'react';
import { render, screen } from '@testing-library/react';
import { vi } from 'vitest';
import { usePage } from '@inertiajs/react';
import Show from './Show';

vi.mock('@inertiajs/react', () => ({
    usePage: vi.fn(),
    Link: ({ children, ...props }) => <a {...props}>{children}</a>,
}));

vi.mock('../../Layouts/TenantLayout', () => ({ default: ({ children }) => <div>{children}</div> }));
vi.mock('../../components/PageTopBar', () => ({
    __esModule: true,
    default: ({ children }) => <div>{children}</div>,
    PageActionsMenu: ({ children }) => <div>{children}</div>,
    PageTopNavigation: () => null,
}));
vi.mock('../../components/PageHeader/PageHeader', () => ({
    PageHeader: ({ children }) => <div>{children}</div>,
    PageHeaderActions: ({ children }) => <div>{children}</div>,
    PageHeaderBreadcrumbs: () => null,
    PageHeaderContent: ({ children }) => <div>{children}</div>,
    PageHeaderMeta: ({ children }) => <div>{children}</div>,
    PageHeaderTitle: ({ children }) => <div>{children}</div>,
}));
vi.mock('../../components/AppHead', () => ({ default: () => null }));
vi.mock('../../components/DateTag', () => ({ default: () => null }));
vi.mock('../../components/DeleteDialog', () => ({ default: () => null }));
vi.mock('../../components/Badge', () => ({ default: ({ children }) => <span>{children}</span> }));
vi.mock('../../components/RiserStack/RiserStackEditor', () => ({ default: () => (
    <svg data-testid="riser-stack-editor" />
)}));
vi.mock('../../hooks/useRoute', () => ({ default: () => ({ route: vi.fn(() => '#') }) }));

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
