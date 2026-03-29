import React, { useState } from 'react';
import { Joyride } from 'react-joyride';
import { STATUS } from 'react-joyride';
import resolveConfig from 'tailwindcss/resolveConfig';
import tailwindConfig from '../../../../tailwind.config';

export const OnboardingTour = ({ run = false, onTourEnd }) => {
	const tw = resolveConfig(tailwindConfig);

	const [steps] = useState([
		{
			target: 'body',
			placement: 'center',
			title: 'Welcome to Choir Concierge!',
			content: "We're so glad you've created your choir. Let's take a quick look around.",
			disableBeacon: true,
		},
		{
			target: '[data-tour="dashboard"]',
			title: 'Dashboard',
			content: 'This is your central hub for all choir activities and recent updates.',
			placement: 'right',
		},
		{
			target: '[data-tour="singers"]',
			title: 'Manage Singers',
			content: 'Add your choir members here, assign voice parts, and manage their roles.',
			placement: 'right',
		},
		{
			target: '[data-tour="songs"]',
			title: 'Music Library',
			content: 'Upload sheet music, learning tracks, and manage your repertoire.',
			placement: 'right',
		},
		{
			target: '[data-tour="events"]',
			title: 'Events & Rehearsals',
			content: 'Schedule your rehearsals and performances, and track attendance.',
			placement: 'right',
		},
		{
			target: 'body',
			placement: 'center',
			title: "You're all set!",
			content: 'Start by adding some singers or uploading your first song. Have fun!',
		},
	]);

	const handleJoyrideCallback = data => {
		const { status } = data;
		const finishedStatuses = [STATUS.FINISHED, STATUS.SKIPPED];

		if (finishedStatuses.includes(status)) {
			if (onTourEnd) {
				onTourEnd();
			}
		}
	};

	return (
		<Joyride
			steps={steps}
			run={run}
			continuous={true}
			showProgress={true}
			showSkipButton={true}
			callback={handleJoyrideCallback}
			options={{
				primaryColor: tw.theme.colors.purple[600],
				zIndex: 1000,
				textColor: tw.theme.colors.gray[600],
			}}
			styles={{
				tooltip: {
					borderRadius: tw.theme.borderRadius.lg,
					padding: '24px',
				},
				tooltipTitle: {
					color: tw.theme.colors.gray[900],
					textAlign: 'left',
				},
				tooltipContent: {
					textAlign: 'left',
					fontSize: '14px',
				},

				buttonPrimary: {
					fontSize: '14px',
					padding: '8px 16px',
				},
				buttonBack: {
					fontSize: '14px',
				},
				buttonSkip: {
					fontSize: '14px',
				},
			}}
		/>
	);
};

export default OnboardingTour;
