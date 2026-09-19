import React from 'react';
import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import '@testing-library/jest-dom';
import SongAttachmentForm from './SongAttachmentForm';

const mockPost = jest.fn();
const mockReset = jest.fn();
const mockSetData = jest.fn();
const mockSetIsOpen = jest.fn();

jest.mock('@inertiajs/react', () => ({
	useForm: () => ({
		data: { attachment_uploads: [], type: 'youtube', url: '', title: '' },
		setData: mockSetData,
		post: mockPost,
		processing: false,
		errors: {},
		reset: mockReset,
	}),
}));

jest.mock('../../hooks/useRoute', () => () => ({ route: name => name }));
jest.mock('../../AttachmentType', () => ({
	__esModule: true,
	default: {
		types: { youtube: {} },
		get: () => ({ title: 'YouTube', textColour: 'text-gray-700', icon: 'youtube' }),
	},
}));
jest.mock('../Dialog', () => ({ title, children, okLabel, onOk }) => (
	<div role="dialog" aria-label={title}>
		{children}
		<button onClick={onOk}>{okLabel}</button>
	</div>
));
jest.mock('../Form', () => ({ children }) => <form>{children}</form>);
jest.mock('../inputs/Label', () => ({ label }) => <label>{label}</label>);
jest.mock('../inputs/Error', () => ({ children }) => <span>{children}</span>);
jest.mock('../inputs/RadioGroup', () => () => <div />);
jest.mock('../inputs/TextInput', () => () => <input />);
jest.mock('../inputs/FileInput', () => () => <input type="file" />);

describe('SongAttachmentForm', () => {
	it('renders inside the modal and submits from the modal action', async () => {
		render(<SongAttachmentForm song={{ id: 12 }} isOpen setIsOpen={mockSetIsOpen} />);

		expect(screen.getByRole('dialog', { name: 'Add Attachment' })).toBeInTheDocument();

		await userEvent.click(screen.getByRole('button', { name: 'Save' }));

		expect(mockPost).toHaveBeenCalledWith('songs.attachments.store', expect.any(Object));
	});
});
