import React from 'react';
import {Link} from '@inertiajs/react';
import type {Method} from '@inertiajs/core';
import buttonStyles from './buttonStylesV2';

type ButtonLinkV2Props = React.AnchorHTMLAttributes<HTMLAnchorElement> & {
	href: string;
	intent?: 'primary' | 'secondary' | 'danger' | 'warning' | 'success';
	weight?: 'solid' | 'outline' | 'clear';
	size?: 'lg' | 'md' | 'sm';
	theme?: 'light' | 'dark';
	external?: boolean;
	method?: Method;
	disabled?: boolean;
};

const ButtonLinkV2 = React.forwardRef(({href, intent = 'secondary', weight = 'solid', size = 'md', theme = 'light', className, children, disabled, onClick, external = false, ...otherProps}: ButtonLinkV2Props, ref: React.Ref<HTMLAnchorElement>) => {
	const props = {href, onClick, className: buttonStyles(intent, weight, size, theme, disabled, className), ref, ...otherProps};

	return external ? <a {...props}>{children}</a> : <Link {...(props as React.ComponentProps<typeof Link>)}>{children}</Link>;
});

export default ButtonLinkV2;