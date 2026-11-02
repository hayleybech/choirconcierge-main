import classNames from "../classNames";
import React from "react";
 
const StyledLink = ({ intent = 'none', className, children, ...extraProps }) => (
	<a
		{...extraProps}
		className={classNames(
			'font-medium',
			{
				primary: 'text-purple-700 no-underline hover:text-sky-500',
				secondary: 'text-purple-700 underline hover:no-underline',
			}[intent],
			className
		)}
	>
		{children}
	</a>
);

export default StyledLink;