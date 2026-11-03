const timeIntervals = [
	{ unit: 'year', seconds: 60 * 60 * 24 * 365 },
	{ unit: 'month', seconds: 60 * 60 * 24 * 30 },
	{ unit: 'week', seconds: 60 * 60 * 24 * 7 },
	{ unit: 'day', seconds: 60 * 60 * 24 },
	{ unit: 'hour', seconds: 60 * 60 },
	{ unit: 'minute', seconds: 60 },
	{ unit: 'second', seconds: 1 },
];

export default function formatRelativeDate(date) {
	const secondsFromNow = (new Date(date).getTime() - Date.now()) / 1000;
	const interval = timeIntervals.find(({ seconds }) => Math.abs(secondsFromNow) >= seconds) ?? timeIntervals.at(-1);

	return new Intl.RelativeTimeFormat(undefined, { numeric: 'auto' }).format(
		Math.round(secondsFromNow / interval.seconds),
		interval.unit
	);
}
