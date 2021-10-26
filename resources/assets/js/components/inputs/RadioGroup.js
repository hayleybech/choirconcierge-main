import React from 'react';
import { RadioGroup as BaseRadioGroup } from '@headlessui/react'
import classNames from "../../classNames";
import Icon from "../Icon";

const RadioGroup = ({ label, options, selected, setSelected, vertical }) => (
    <BaseRadioGroup value={selected} onChange={setSelected}>
        <BaseRadioGroup.Label>{label}</BaseRadioGroup.Label>
        <div className={classNames(
            'mt-1 bg-white rounded-md -space-y-px flex text-left',
            vertical ? 'flex-col' : 'flex-col md:flex-row',
        )}>
            {options.map((option, index) => (
                <BaseRadioGroup.Option
                    key={option.name}
                    value={option.id}
                    disabled={option.disabled ?? false}
                    className={({ checked }) =>
                        classNames(
                            'relative border p-4 flex cursor-pointer focus:outline-none flex-grow items-center',
                            (vertical && index === 0) && 'rounded-tl-md rounded-tr-md',
                            (vertical && index === options.length - 1) && 'rounded-bl-md rounded-br-md',
                            (!vertical && index === 0) && 'rounded-tl-md rounded-bl-md',
                            (!vertical && index === options.length - 1) && 'rounded-tr-md rounded-br-md',
                            checked ? 'bg-purple-100 border-purple-300 z-10' : 'border-gray-200',
                        )
                    }
                >
                    {({ active, checked }) => (
                        <>
                            <span
                                className={classNames(
                                    'h-4 w-4 mt-0.5 cursor-pointer rounded-full border flex items-center justify-center flex-shrink-0',
                                    checked ? 'bg-purple-600 border-transparent' : 'bg-white border-gray-300',
                                    active && 'ring-2 ring-offset-2 ring-purple-500',
                                )}
                                aria-hidden="true"
                            >
                              <span className="rounded-full bg-white w-1.5 h-1.5 flex-shrink-0" />
                            </span>

                            {option.icon && <Icon icon={option.icon} mr className={classNames(
                                'text-lg ml-3',
                                option.colour && `text-${option.colour}`,
                                (checked && !option.colour) && 'text-purple-700',
                                (!checked && !option.colour) && 'text-gray-900',
                                option.disabled && 'text-opacity-50',
                            )} />}

                            <div className="ml-3 flex flex-col">
                                <BaseRadioGroup.Label
                                    as="span"
                                    className={classNames(
                                        'block text-sm font-medium',
                                        checked ? 'text-purple-900' : 'text-gray-900',
                                        option.disabled && 'text-opacity-50',
                                    )}
                                >
                                    {option.name}
                                </BaseRadioGroup.Label>
                                {option.description && (
                                    <BaseRadioGroup.Description
                                        as="div"
                                        className={classNames(
                                            'block text-sm mt-1.5',
                                            checked ? 'text-purple-700' : 'text-gray-500',
                                            option.disabled && 'text-opacity-50',
                                        )}
                                    >
                                        {option.description}
                                    </BaseRadioGroup.Description>
                                )}
                            </div>
                        </>
                    )}
                </BaseRadioGroup.Option>
            ))}
        </div>
    </BaseRadioGroup>
);

export default RadioGroup;
