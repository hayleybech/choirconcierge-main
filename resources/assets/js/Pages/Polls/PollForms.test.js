import React from 'react';
import { render, screen } from '@testing-library/react';
import { useForm } from '@inertiajs/react';
import Create from './Create';
import Edit from './Edit';

jest.mock('@inertiajs/react', () => ({
	useForm: jest.fn(),
	Link: ({ children, ...props }) => <a {...props}>{children}</a>,
}));

jest.mock('../../Layouts/TenantLayout', () => ({ children }) => require('react').createElement('div', null, children));
jest.mock('../../components/AppHead', () => () => null);
jest.mock('../../components/PageTopBar', () => {
	const React = require('react');

	return {
		__esModule: true,
		default: ({ children }) => React.createElement('div', null, children),
		PageTopNavigation: () => null,
	};
});
jest.mock('../../components/PageHeader/PageHeader', () => {
	const React = require('react');
	const Container = ({ children }) => React.createElement('div', null, children);

	return {
		PageHeader: Container,
		PageHeaderBreadcrumbs: () => null,
		PageHeaderContent: Container,
		PageHeaderTitle: Container,
	};
});
jest.mock('../../hooks/useRoute', () => () => ({ route: name => `/${name.replace(/\./g, '/')}` }));
jest.mock('../../components/FormWrapper', () => ({ children }) => require('react').createElement('div', null, children));
jest.mock('../../components/Form', () => ({ children, onSubmit }) =>
	require('react').createElement('form', { onSubmit }, children),
);
jest.mock('../../components/FormSection', () => ({ children }) => require('react').createElement('div', null, children));
jest.mock('../../components/inputs/TextInput', () => () => null);
jest.mock('../../components/inputs/Label', () => () => null);
jest.mock('../../components/inputs/CheckboxWithLabel', () => () => null);
jest.mock('../../components/inputs/Date', () => () => null);
jest.mock('../../components/inputs/Time', () => () => null);
jest.mock('../../components/inputs/Help', () => () => null);
jest.mock('../../components/inputs/CheckboxGroup', () => () => null);
jest.mock('../../components/inputs/RichTextInput', () => () => null);
jest.mock('../../components/Icon', () => () => null);

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
			setData: jest.fn(),
			post: jest.fn(),
			processing: false,
			errors: {},
			transform: jest.fn(),
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
});
