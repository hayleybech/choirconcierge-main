import React from 'react';
import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { vi } from 'vitest';
import '@testing-library/jest-dom';
import SongAttachmentForm from './SongAttachmentForm';

const { mockPost, mockReset, mockSetData, mockSetIsOpen } = vi.hoisted(() => ({
	mockPost: vi.fn(),
	mockReset: vi.fn(),
	mockSetData: vi.fn(),
	mockSetIsOpen: vi.fn(),
}));

vi.mock('@inertiajs/react', () => ({
	useForm: () => ({
		data: { attachment_uploads: [], type: 'youtube', url: '', title: '' },
		setData: mockSetData,
		post: mockPost,
		processing: false,
		errors: {},
		reset: mockReset,
	}),
}));

vi.mock('../../hooks/useRoute', () => ({ default: () => ({ route: name => name }) }));
vi.mock('../../AttachmentType', () => ({
	__esModule: true,
	default: {
		types: { youtube: {} },
		get: () => ({ title: 'YouTube', textColour: 'text-gray-700', icon: 'youtube' }),
	},
}));

vi.mock('../Dialog', () => ({ default: ({ title, children, okLabel, onOk }) => (
	<div role="dialog" aria-label={title}>
		{children}
		<button onClick={onOk}>{okLabel}</button>
	</div>
)}));
vi.mock('../Form', () => ({ default: ({ children }) => <form>{children}</form> }));
vi.mock('../inputs/Label', () => ({ default: ({ label }) => <label>{label}</label> }));
vi.mock('../inputs/Error', () => ({ default: ({ children }) => <span>{children}</span> }));
vi.mock('../inputs/RadioGroup', () => ({ default: () => <div /> }));
vi.mock('../inputs/TextInput', () => ({ default: () => <input /> }));
vi.mock('../inputs/FileInput', () => ({ default: () => <input type="file" /> }));

describe('SongAttachmentForm', () => {
	it('renders inside the modal and submits from the modal action', async () => {
		render(<SongAttachmentForm song={{ id: 12 }} isOpen setIsOpen={mockSetIsOpen} />);

		expect(screen.getByRole('dialog', { name: 'Add Attachment' })).toBeInTheDocument();

		await userEvent.click(screen.getByRole('button', { name: 'Save' }));

		expect(mockPost).toHaveBeenCalledWith('songs.attachments.store', expect.any(Object));
	});
});
