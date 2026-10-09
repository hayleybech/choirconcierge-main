import React from 'react';
import type {Method} from '@inertiajs/core';
import buttonStyles from './buttonStylesV2';
import ButtonLink from './ButtonLinkV2';

type ButtonV2Props = React.ButtonHTMLAttributes<HTMLButtonElement> & {
	intent?: 'primary' | 'secondary' | 'danger' | 'warning' | 'success';
	weight?: 'solid' | 'outline' | 'clear';
	size?: 'lg' | 'md' | 'sm';
	theme?: 'light' | 'dark';
	href?: string;
	method?: Method;
};

const ButtonV2 = React.forwardRef(
	({intent = 'secondary', weight = 'solid', size = 'md', theme = 'light', type = 'button', href, method, className, children, disabled, ...otherProps}: ButtonV2Props, ref: React.Ref<HTMLButtonElement>) =>
		href ? (
			<ButtonLink href={href} intent={intent} weight={weight} size={size} theme={theme} className={className} disabled={disabled} method={method} ref={ref as React.Ref<HTMLAnchorElement>} {...(otherProps as unknown as Omit<React.ComponentProps<typeof ButtonLink>, 'href'>)}>
				{children}
			</ButtonLink>
		) : (
			<button className={buttonStyles(intent, weight, size, theme, disabled, className)} disabled={disabled} ref={ref} type={type} {...otherProps}>
				{children}
			</button>
		),
);

export default ButtonV2;