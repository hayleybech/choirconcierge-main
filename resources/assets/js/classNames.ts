import clsx, { ClassValue } from 'clsx';
import { twMerge } from 'tailwind-merge';

const classNames = (...classes: string[]): string => {
    return classes.filter(Boolean).join(' ');
};

export const twcx = (...inputs: ClassValue[]) => {
	return twMerge(clsx(...inputs));
};

export default classNames;