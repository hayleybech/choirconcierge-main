import classNames from "../classNames";
import React from "react";

const StyledLink = ({ variant = 'primary', className, children, ...extraProps }) => (
	<a
		{...extraProps}
		className={classNames(
			'font-medium',
			variant === 'primary' ? 'text-purple-600 hover:text-purple-500' : 'text-gray-500 hover:text-gray-600',
			className
		)}
	>
		{children}
	</a>
);

export default StyledLink;