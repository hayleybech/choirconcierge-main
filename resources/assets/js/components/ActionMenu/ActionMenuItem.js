import React from 'react';
import { Link } from '@inertiajs/react';
import menuItemStyles from './menuItemStyles';
import { Menu } from '@headlessui/react';

const ActionMenuItem = ({ url, onClick, download, variant, children, method }) => {
    return (
        <Menu.Item>
            {({ active }) => {
                if (url) {
                    if (download) {
                        return (
                            <a
                                href={url}
                                className={menuItemStyles(variant, active)}
                                download={download}
                            >
                                {children}
                            </a>
                        );
                    } else {
                        return (
                            <Link
                                href={url}
                                className={menuItemStyles(variant, active)}
								method={method}
                            >
                                {children}
                            </Link>
                        );
                    }
                } else {
                    return (
                        <button
                            onClick={onClick}
                            className={menuItemStyles(variant, active)}
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
