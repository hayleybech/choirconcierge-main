import classNames from '../classNames';
import React from 'react';

const StyledLink = ({ intent = 'none', href, className, children, ...extraProps }) =>
	href ? (
		<a href={href} {...extraProps} className={linkStyles(intent, className)}>
			{children}
		</a>
	) : (
		<button type="button" {...extraProps} className={linkStyles(intent, className)}>
			{children}
		</button>
	);

export default StyledLink;

export const linkStyles = (intent, className) =>
	classNames(
		'font-medium cursor-pointer hover:text-sky-500',
		{
			primary: 'text-purple-700 no-underline',
			secondary: 'text-purple-700 underline',
		}[intent],
		className
	);
