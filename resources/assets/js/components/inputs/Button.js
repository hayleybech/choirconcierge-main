import React from 'react';
import buttonStyles from './buttonStyles';
import ButtonLink from './ButtonLink';

const Button = React.forwardRef(
	({ variant = 'secondary', size = 'md', type = 'button', href, method, className, children, disabled, ...otherProps }, ref) =>
		href ? (
			<ButtonLink
				href={href}
				variant={variant}
				size={size}
				className={className}
				disabled={disabled}
				method={method}
				as={method ? 'button' : 'a'}
				ref={ref}
				{...otherProps}
			>
				{children}
			</ButtonLink>
		) : (
			<button
				className={buttonStyles(variant, size, disabled, className)}
				disabled={disabled}
				ref={ref}
				type={type}
				{...otherProps}
			>
				{children}
			</button>
		)
);

export default Button;
