import React from 'react';
import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { vi } from 'vitest';
import '@testing-library/jest-dom';
import SongAttachmentList from './SongAttachmentList';

vi.mock('@inertiajs/react', () => ({
	useForm: () => ({ data: { filename: '', url: '', title: '' }, setData: () => {}, put: () => {}, errors: {} }),
}));
vi.mock('../../hooks/useRoute', () => ({ default: () => ({ route: name => name }) }));
vi.mock('../../AttachmentType', () => ({
	__esModule: true,
	default: {
		get: type => ({
			title: type,
			isAudio: type === 'learning-tracks',
			isPdf: type === 'sheet-music',
			isVideo: type === 'youtube',
		}),
	},
}));
vi.mock('../inputs/ButtonV2', () => ({
	default: ({ href, download, target, onClick, children }) => href
		? <a href={href} download={download} target={target} onClick={onClick}>{children}</a>
		: <button onClick={onClick}>{children}</button>,
}));
vi.mock('../DeleteDialog', () => ({ default: () => null }));
vi.mock('../Dialog', () => ({ default: () => null }));
vi.mock('./SongAttachmentForm', () => ({ default: () => null }));

const track = {
	id: 1,
	type: 'learning-tracks',
	title: 'Learning track',
	filepath: 'track.mp3',
	download_url: '/track.mp3',
};
const score = {
	id: 2,
	type: 'sheet-music',
	title: 'Sheet music',
	filepath: 'score.pdf',
	download_url: '/score.pdf',
};

const renderAttachmentList = (can = { update_song: true }, setCurrentPdf = vi.fn(), player = { src: null, play: vi.fn() }) =>
	render(
		<SongAttachmentList
			attachmentTypes={{ 'learning-tracks': [track], 'sheet-music': [score] }}
			song={{ can }}
			currentPdf={null}
			setCurrentPdf={setCurrentPdf}
			player={player}
		/>
	);

describe('SongAttachmentList', () => {
	it('moves multiple secondary actions into an overflow menu and preserves primary actions', async () => {
		const user = userEvent.setup();
		const player = { src: null, play: vi.fn() };
		const setCurrentPdf = vi.fn();
		renderAttachmentList({ update_song: true }, setCurrentPdf, player);

		const menuButtons = screen.getAllByRole('button', { name: 'Open attachment actions' });
		expect(menuButtons).toHaveLength(2);

		await user.click(menuButtons[0]);
		expect((await screen.findByRole('menu')).closest('nav')).toBeNull();
		expect(screen.getByRole('menuitem', { name: 'Edit' })).toBeInTheDocument();
		expect(screen.getByRole('menuitem', { name: 'Delete' })).toBeInTheDocument();
		expect(screen.getByRole('menuitem', { name: 'Download' })).toHaveAttribute('href', '/track.mp3');

		await user.click(screen.getByRole('link', { name: 'Learning track' }));
		expect(player.play).toHaveBeenCalledWith(track);
		await user.click(screen.getByRole('link', { name: 'Sheet music' }));
		expect(setCurrentPdf).toHaveBeenCalledWith(score);
	});

	it('keeps a single secondary download action visible without an overflow menu', () => {
		renderAttachmentList({});

		expect(screen.queryByRole('button', { name: 'Open attachment actions' })).not.toBeInTheDocument();
		expect(document.querySelector('a[href="/track.mp3"]')).toHaveAttribute('download', 'track.mp3');
	});
});