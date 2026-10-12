import { DateTime } from 'luxon';

export function dateTimeFromISO(value) {
    if (!value) {
        return DateTime.now();
    }

    const dateTime = DateTime.fromISO(value);

    return dateTime.isValid ? dateTime : DateTime.now();
}

export function setTime(source, target) {
    if (!source.isValid || !target.isValid) {
        return target;
    }

    return target.set({
        hour: source.hour,
        minute: source.minute,
    });
}