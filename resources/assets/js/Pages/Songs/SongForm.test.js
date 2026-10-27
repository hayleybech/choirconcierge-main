import React from 'react';
import { fireEvent, render, screen } from '@testing-library/react';
import { useForm } from '@inertiajs/react';
import { vi } from 'vitest';
import SongForm from './SongForm';

vi.mock('@inertiajs/react', () => ({ useForm: vi.fn() }));
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
vi.mock('../../components/inputs/CheckboxGroup', () => ({ default: () => null }));
vi.mock('../../components/inputs/RadioGroup', () => ({ default: () => null }));
vi.mock('../../components/inputs/Help', () => ({ default: () => null }));
vi.mock('../../components/inputs/Select', () => ({ default: () => null }));
vi.mock('../../components/inputs/ButtonLinkV2', async () => {
	const React = await vi.importActual('react');
	return { default: ({ href, children }) => React.createElement('a', { href }, children) };
});
vi.mock('../../components/inputs/ButtonV2', async () => {
	const React = await vi.importActual('react');
	return { default: ({ children, ...props }) => React.createElement('button', props, children) };
});
vi.mock('../../components/inputs/Error', () => ({ default: () => null }));
vi.mock('../../components/inputs/RichTextInput', () => ({ default: () => null }));
vi.mock('../../hooks/useRoute', () => ({ default: () => ({ route: name => `/${name.replace(/\./g, '/')}` }) }));

describe('SongForm notifications', () => {
	beforeEach(() => {
		useForm.mockImplementation(initialData => {
			const [data, setData] = React.useState(initialData);

			return {
				data,
				setData: (key, value) => setData(current => ({ ...current, [key]: value })),
				post: vi.fn(),
				put: vi.fn(),
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
