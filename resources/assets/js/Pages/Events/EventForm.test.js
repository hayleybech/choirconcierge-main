import React from 'react';
import { fireEvent, render, screen } from '@testing-library/react';
import { useForm, usePage } from '@inertiajs/react';
import EventForm from './EventForm';

jest.mock('@inertiajs/react', () => ({ useForm: jest.fn(), usePage: jest.fn() }));
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
jest.mock('../../components/inputs/Error', () => () => null);
jest.mock('../../components/inputs/CheckboxGroup', () => () => null);
jest.mock('../../components/inputs/RadioGroup', () => () => null);
jest.mock('../../components/inputs/Help', () => () => null);
jest.mock('../../components/inputs/ButtonLinkV2', () => ({ href, children }) =>
	require('react').createElement('a', { href }, children),
);
jest.mock('../../components/inputs/ButtonV2', () => ({ children, ...props }) =>
	require('react').createElement('button', props, children),
);
jest.mock('../../components/inputs/LocationInput', () => () => null);
jest.mock('../../components/inputs/DateCalendarInput', () => () => null);
jest.mock('../../components/inputs/Time', () => () => null);
jest.mock('../../components/inputs/DetailToggle', () => () => null);
jest.mock('../../components/inputs/RichTextInput', () => () => null);
jest.mock('../../hooks/useRoute', () => () => ({ route: name => `/${name.replace(/\./g, '/')}` }));

describe('EventForm notifications', () => {
	beforeEach(() => {
		usePage.mockReturnValue({ props: { tenant: { timezone_label: 'UTC' } } });
		useForm.mockImplementation(initialData => {
			const [data, setData] = React.useState(initialData);

			return {
				data,
				setData: (key, value) => setData(current => ({ ...current, [key]: value })),
				post: jest.fn(),
				put: jest.fn(),
				processing: false,
				errors: {},
				transform: jest.fn(),
			};
		});
	});

	it('omits the notification toggle and note from event creation', () => {
		render(<EventForm types={[]} ensembles={[]} />);

		expect(screen.queryByRole('checkbox', { name: /notification/i })).toBeNull();
		expect(screen.queryByText('Message (optional)')).toBeNull();
	});

	it('keeps the notification toggle and optional note on event updates', () => {
		render(
			<EventForm
				types={[]}
					ensembles={[]}
				event={{
					id: 1,
					title: 'Event',
					type: { id: 1 },
					location_name: '',
					location_place_id: '',
					location_icon: '',
					location_address: '',
					description: '',
					ensembles: [],
					is_repeating: false,
					start_date: '2026-10-24T18:00:00Z',
					end_date: '2026-10-24T20:00:00Z',
					call_time: '2026-10-24T17:30:00Z',
				}}
			/>,
		);

		fireEvent.click(screen.getByRole('checkbox', { name: 'Send "Event Updated" notification to singers?' }));

		expect(screen.getByText('Message (optional)')).toBeTruthy();
	});
});
