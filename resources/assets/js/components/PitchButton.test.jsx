import React from 'react';
import { fireEvent, render, waitFor } from '@testing-library/react';
import { vi } from 'vitest';
import { loaded, start } from 'tone';
import PitchButton from './PitchButton';

vi.mock('tone', () => ({
	loaded: vi.fn(),
	start: vi.fn(),
}));

describe('PitchButton', () => {
	it('waits for Tone to start and load before triggering a note', async () => {
		const calls = [];
		start.mockImplementation(() => {
			calls.push('start');
			return Promise.resolve();
		});
		loaded.mockImplementation(() => {
			calls.push('loaded');
			return Promise.resolve();
		});
		const instrument = { instrument: { triggerAttack: vi.fn() } };
		const { getByRole } = render(<PitchButton instrument={instrument} note="C" />);

		fireEvent.mouseDown(getByRole('button'));

		await waitFor(() => expect(instrument.instrument.triggerAttack).toHaveBeenCalledWith('C4'));

		expect(calls).toEqual(['start', 'loaded']);
	});
});