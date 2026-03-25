import React from 'react';
import { Link } from '@inertiajs/react';
import menuItemStyles from './menuItemStyles';
import { Menu } from '@headlessui/react';

const ActionMenuItem = ({ url, onClick, download, variant, children, method, disabled }) => {
    return (
        <Menu.Item>
            {({ active }) => {
                if (url) {
                    if (download) {
                        return (
                            <a
                                href={disabled ? undefined: url}
                                className={menuItemStyles(variant, active, disabled)}
                                download={download}
                            >
                                {children}
                            </a>
                        );
                    } else {
                        return (
                            <Link
                                href={disabled ? undefined : url}
                                className={menuItemStyles(variant, active, disabled)}
								method={method}
                            >
                                {children}
                            </Link>
                        );
                    }
                } else {
                    return (
                        <button
                            onClick={disabled ? undefined : onClick}
                            className={menuItemStyles(variant, active, disabled)}
                        >
                            {children}
                        </button>
                    );
                }
            }}
        </Menu.Item>
    );
};

export default ActionMenuItem;
