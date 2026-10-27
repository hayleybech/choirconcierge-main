import React from 'react';
import { render } from '@testing-library/react';
import VoicePartTag, { voicePartTextColourClasses } from './VoicePartTag';

describe('VoicePartTag', () => {
	it('uses the configured voice-part background colour', () => {
		const { getByText } = render(<VoicePartTag title="Soprano" colour="blue" />);

		expect(getByText('Soprano').className).toContain('bg-blue-500/75');
	});

	it('provides explicit text colour classes for voice-part selectors', () => {
		expect(voicePartTextColourClasses.blue).toBe('text-blue-500');
		expect(voicePartTextColourClasses.gray).toBe('text-gray-500');
	});
});
