import classNames from '../classNames';
import React from 'react';

const StyledLink = ({ intent = 'none', href, className, children, ...extraProps }) =>
	href ? (
		<a {...extraProps} className={linkStyles(intent, className)}>
			{children}
		</a>
	) : (
		<button type="button" {...extraProps} className={linkStyles(intent, className)}>
			{children}
		</button>
	);

export default StyledLink;

const linkStyles = (intent, className) =>
	classNames(
		'font-medium',
		{
			primary: 'text-purple-700 no-underline hover:text-sky-500',
			secondary: 'text-purple-700 underline hover:no-underline',
		}[intent],
		className
	);
