import React from 'react';
import { fireEvent, render, screen } from '@testing-library/react';
import { useForm } from '@inertiajs/react';
import SongForm from './SongForm';

jest.mock('@inertiajs/react', () => ({ useForm: jest.fn() }));
jest.mock('../../components/FormSection', () => {
	const React = require('react');

	return ({ title, children }) => React.createElement('section', null, React.createElement('h2', null, title), children);
});
jest.mock('../../components/inputs/CheckboxWithLabel', () => {
	const React = require('react');

	return ({ label, id, checked, onChange }) =>
		React.createElement(
			'label',
			{ htmlFor: id },
			label,
			React.createElement('input', { id, type: 'checkbox', checked: !!checked, onChange }),
		);
});
jest.mock('../../components/inputs/Label', () => ({ label }) => require('react').createElement('span', null, label));
jest.mock('../../components/inputs/TextareaInput', () => ({ name, value, updateFn }) =>
	require('react').createElement('textarea', { name, value, onChange: event => updateFn(event.target.value) }),
);
jest.mock('../../components/FormWrapper', () => ({ children }) => require('react').createElement('div', null, children));
jest.mock('../../components/Form', () => ({ children, onSubmit }) =>
	require('react').createElement('form', { onSubmit }, children),
);
jest.mock('../../components/FormFooter', () => ({ children }) => require('react').createElement('div', null, children));
jest.mock('../../components/inputs/TextInput', () => () => null);
jest.mock('../../components/inputs/CheckboxGroup', () => () => null);
jest.mock('../../components/inputs/RadioGroup', () => () => null);
jest.mock('../../components/inputs/Help', () => () => null);
jest.mock('../../components/inputs/Select', () => () => null);
jest.mock('../../components/inputs/ButtonLinkV2', () => ({ href, children }) =>
	require('react').createElement('a', { href }, children),
);
jest.mock('../../components/inputs/ButtonV2', () => ({ children, ...props }) =>
	require('react').createElement('button', props, children),
);
jest.mock('../../components/inputs/Error', () => () => null);
jest.mock('../../components/inputs/RichTextInput', () => () => null);
jest.mock('../../hooks/useRoute', () => () => ({ route: name => `/${name.replace(/\./g, '/')}` }));

describe('SongForm notifications', () => {
	beforeEach(() => {
		useForm.mockImplementation(initialData => {
			const [data, setData] = React.useState(initialData);

			return {
				data,
				setData: (key, value) => setData(current => ({ ...current, [key]: value })),
				post: jest.fn(),
				put: jest.fn(),
				processing: false,
				errors: {},
			};
		});
	});

	it('omits the notification toggle and note from song creation', () => {
		render(<SongForm categories={[]} statuses={[]} pitches={{}} ensembles={[]} />);

		expect(screen.queryByRole('checkbox', { name: /notification/i })).toBeNull();
		expect(screen.queryByText('Message (optional)')).toBeNull();
	});

	it('keeps the notification toggle and optional note on song updates', () => {
		render(
			<SongForm
				categories={[]}
				statuses={[]}
				pitches={{}}
				ensembles={[]}
				song={{
					id: 1,
					title: 'Song',
					categories: [],
					ensembles: [],
					status: { id: 1 },
					pitch_blown: 0,
					show_for_prospects: false,
				}}
			/>,
		);

		fireEvent.click(screen.getByRole('checkbox', { name: 'Send "Song Updated" notification to singers?' }));

		expect(screen.getByText('Message (optional)')).toBeTruthy();
	});
});
