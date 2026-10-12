import { DateTime } from 'luxon';
import { dateTimeFromISO, setTime } from './eventDateTime';

describe('eventDateTime', () => {
    it('falls back to a valid date for malformed event dates', () => {
        expect(dateTimeFromISO('not-a-date').isValid).toBe(true);
    });

    it('does not merge invalid time values into event dates', () => {
        const target = DateTime.fromISO('2025-01-01T10:30:00');
        const result = setTime(DateTime.fromISO('not-a-time'), target);

        expect(result).toBe(target);
    });

    it('merges valid time values into event dates', () => {
        const target = DateTime.fromISO('2025-01-01T10:30:00');
        const result = setTime(DateTime.fromISO('2025-01-01T14:45:00'), target);

        expect(result.hour).toBe(14);
        expect(result.minute).toBe(45);
    });
});