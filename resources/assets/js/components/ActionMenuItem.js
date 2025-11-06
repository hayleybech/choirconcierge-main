import React from 'react';
import { Link } from '@inertiajs/react';
import classNames from '../classNames';
import { Menu } from '@headlessui/react';

const ActionMenuItem = ({ url, onClick, download, variant, children }) => {
    return (
        <Menu.Item>
            {({ active }) => {
                if (url) {
                    if (download) {
                        return (
                            <a
                                href={url}
                                className={classNames(
                                    'block w-full text-left px-4 py-2 text-sm',
                                    active ? 'bg-gray-100' : '',
                                    variant === 'danger-outline'
                                        ? 'text-red-500'
                                        : 'text-gray-700'
                                )}
                                download={download}
                            >
                                {children}
                            </a>
                        );
                    } else {
                        return (
                            <Link
                                href={url}
                                className={classNames(
                                    'block w-full text-left px-4 py-2 text-sm',
                                    active ? 'bg-gray-100' : '',
                                    variant === 'danger-outline'
                                        ? 'text-red-500'
                                        : 'text-gray-700'
                                )}
                            >
                                {children}
                            </Link>
                        );
                    }
                } else {
                    return (
                        <button
                            onClick={onClick}
                            className={classNames(
                                'block w-full text-left px-4 py-2 text-sm',
                                active ? 'bg-gray-100' : '',
                                variant === 'danger-outline'
                                    ? 'text-red-500'
                                    : '',
                                variant === 'success-solid'
                                    ? 'text-emerald-500'
                                    : '',
                                variant !== 'danger-outline' &&
                                    variant !== 'success-solid'
                                    ? 'text-gray-700'
                                    : ''
                            )}
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
