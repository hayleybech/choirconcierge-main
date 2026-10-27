import React from 'react';
import { render, screen } from '@testing-library/react';
import { useForm } from '@inertiajs/react';
import { vi } from 'vitest';
import Create from './Create';
import Edit from './Edit';

vi.mock('@inertiajs/react', async () => {
	const React = await vi.importActual('react');
	return {
		useForm: vi.fn(),
		Link: ({ children, ...props }) => React.createElement('a', props, children),
	};
});

vi.mock('../../Layouts/TenantLayout', async () => {
	const React = await vi.importActual('react');
	return { default: ({ children }) => React.createElement('div', null, children) };
});
vi.mock('../../components/AppHead', () => ({ default: () => null }));
vi.mock('../../components/PageTopBar', async () => {
	const React = await vi.importActual('react');

	return {
		__esModule: true,
		default: ({ children }) => React.createElement('div', null, children),
		PageTopNavigation: () => null,
	};
});
vi.mock('../../components/PageHeader/PageHeader', async () => {
	const React = await vi.importActual('react');
	const Container = ({ children }) => React.createElement('div', null, children);

	return {
		PageHeader: Container,
		PageHeaderBreadcrumbs: () => null,
		PageHeaderContent: Container,
		PageHeaderTitle: Container,
	};
});
vi.mock('../../hooks/useRoute', () => ({ default: () => ({ route: name => `/${name.replace(/\./g, '/')}` }) }));
vi.mock('../../components/FormWrapper', async () => {
	const React = await vi.importActual('react');
	return { default: ({ children }) => React.createElement('div', null, children) };
});
vi.mock('../../components/Form', async () => {
	const React = await vi.importActual('react');
	return { default: ({ children, onSubmit }) => React.createElement('form', { onSubmit }, children) };
});
vi.mock('../../components/FormSection', async () => {
	const React = await vi.importActual('react');
	return { default: ({ children }) => React.createElement('div', null, children) };
});
vi.mock('../../components/inputs/TextInput', () => ({ default: () => null }));
vi.mock('../../components/inputs/Label', () => ({ default: () => null }));
vi.mock('../../components/inputs/CheckboxWithLabel', async () => {
	const React = await vi.importActual('react');

	return {
		default: ({ label, id, checked, onChange }) =>
			React.createElement(
				'label',
				{ htmlFor: id },
				label,
				React.createElement('input', { id, type: 'checkbox', checked: !!checked, onChange }),
			),
	};
});
vi.mock('../../components/inputs/DateCalendarInput', () => ({ default: () => null }));
vi.mock('../../components/inputs/Time', () => ({ default: () => null }));
vi.mock('../../components/inputs/Help', () => ({ default: () => null }));
vi.mock('../../components/inputs/CheckboxGroup', () => ({ default: () => null }));
vi.mock('../../components/inputs/RichTextInput', () => ({ default: () => null }));
vi.mock('../../components/Icon', () => ({ default: () => null }));

const formData = {
	title: '',
	description: '',
	can_vote_multiple: false,
	close_at: null,
	options: ['', ''],
	send_notification: true,
	ensemble_ids: [],
};

describe('Poll forms', () => {
	beforeEach(() => {
		useForm.mockReturnValue({
			data: formData,
			setData: vi.fn(),
			post: vi.fn(),
			processing: false,
			errors: {},
			transform: vi.fn(),
		});
	});

	it.each([
		['create', <Create ensembles={[]} />],
		[
			'update',
			<Edit
				poll={{
					id: 1,
					title: 'Poll',
					description: '',
					can_vote_multiple: false,
					close_at: null,
					options: [],
					ensembles: [],
				}}
				ensembles={[]}
			/>,
		],
	])('provides a cancel link at the bottom of the %s form', (_formType, form) => {
		render(form);

		expect(screen.getByRole('link', { name: 'Cancel' }).getAttribute('href')).toBe('/polls/index');
	});

	it('does not expose notification controls on poll creation', () => {
		render(<Create ensembles={[]} />);

		expect(screen.queryByRole('checkbox', { name: 'Send notification to singers?' })).toBeNull();
		expect(screen.queryByText('Message (optional)')).toBeNull();
	});

	it('lets poll creators choose anonymous voting', () => {
		render(<Create ensembles={[]} />);

		expect(screen.getByRole('checkbox', { name: 'Anonymous voting' })).not.toBeNull();
	});

	it('does not let poll editors change anonymous voting', () => {
		render(
			<Edit
				poll={{
					id: 1,
					title: 'Anonymous poll',
					description: '',
					can_vote_multiple: false,
					is_anonymous: true,
					close_at: null,
					options: [],
					ensembles: [],
				}}
				ensembles={[]}
			/>
		);

		expect(screen.queryByRole('checkbox', { name: 'Anonymous voting' })).toBeNull();
	});

	it('includes option IDs in poll edit form data', () => {
		render(
			<Edit
				poll={{
					id: 1,
					title: 'Poll',
					description: '',
					can_vote_multiple: false,
					close_at: null,
					options: [{ id: 42, label: 'Blue' }],
					ensembles: [],
				}}
				ensembles={[]}
			/>
		);

		expect(useForm).toHaveBeenCalledWith(expect.objectContaining({
			options: ['Blue'],
			option_ids: [42],
		}));
	});
});
