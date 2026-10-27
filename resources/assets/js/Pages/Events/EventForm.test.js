import React from 'react';
import { fireEvent, render, screen } from '@testing-library/react';
import { useForm, usePage } from '@inertiajs/react';
import { vi } from 'vitest';
import EventForm from './EventForm';

vi.mock('@inertiajs/react', () => ({ useForm: vi.fn(), usePage: vi.fn() }));
vi.mock('../../components/FormSection', async () => {
	const React = await vi.importActual('react');

	return { default: ({ title, children }) => React.createElement('section', null, React.createElement('h2', null, title), children) };
});
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
vi.mock('../../components/inputs/Label', async () => {
	const React = await vi.importActual('react');
	return { default: ({ label }) => React.createElement('span', null, label) };
});
vi.mock('../../components/inputs/TextareaInput', async () => {
	const React = await vi.importActual('react');
	return { default: ({ name, value, updateFn }) => React.createElement('textarea', { name, value, onChange: event => updateFn(event.target.value) }) };
});
vi.mock('../../components/FormWrapper', async () => {
	const React = await vi.importActual('react');
	return { default: ({ children }) => React.createElement('div', null, children) };
});
vi.mock('../../components/Form', async () => {
	const React = await vi.importActual('react');
	return { default: ({ children, onSubmit }) => React.createElement('form', { onSubmit }, children) };
});
vi.mock('../../components/FormFooter', async () => {
	const React = await vi.importActual('react');
	return { default: ({ children }) => React.createElement('div', null, children) };
});
vi.mock('../../components/inputs/TextInput', () => ({ default: () => null }));
vi.mock('../../components/inputs/Error', () => ({ default: () => null }));
vi.mock('../../components/inputs/CheckboxGroup', () => ({ default: () => null }));
vi.mock('../../components/inputs/RadioGroup', () => ({ default: () => null }));
vi.mock('../../components/inputs/Help', () => ({ default: () => null }));
vi.mock('../../components/inputs/ButtonLinkV2', async () => {
	const React = await vi.importActual('react');
	return { default: ({ href, children }) => React.createElement('a', { href }, children) };
});
vi.mock('../../components/inputs/ButtonV2', async () => {
	const React = await vi.importActual('react');
	return { default: ({ children, ...props }) => React.createElement('button', props, children) };
});
vi.mock('../../components/inputs/LocationInput', () => ({ default: () => null }));
vi.mock('../../components/inputs/DateCalendarInput', () => ({ default: () => null }));
vi.mock('../../components/inputs/Time', () => ({ default: () => null }));
vi.mock('../../components/inputs/DetailToggle', () => ({ default: () => null }));
vi.mock('../../components/inputs/RichTextInput', () => ({ default: () => null }));
vi.mock('../../hooks/useRoute', () => ({ default: () => ({ route: name => `/${name.replace(/\./g, '/')}` }) }));

describe('EventForm notifications', () => {
	beforeEach(() => {
		usePage.mockReturnValue({ props: { tenant: { timezone_label: 'UTC' } } });
		useForm.mockImplementation(initialData => {
			const [data, setData] = React.useState(initialData);

			return {
				data,
				setData: (key, value) => setData(current => ({ ...current, [key]: value })),
				post: vi.fn(),
				put: vi.fn(),
				processing: false,
				errors: {},
				transform: vi.fn(),
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
