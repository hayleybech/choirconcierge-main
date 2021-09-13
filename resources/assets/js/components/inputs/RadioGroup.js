import React from 'react';
import { RadioGroup as BaseRadioGroup } from '@headlessui/react'
import classNames from "../../classNames";

const RadioGroup = ({ label, options, selected, setSelected }) => {

    return (
        <BaseRadioGroup value={selected} onChange={setSelected}>
            <BaseRadioGroup.Label>{label}</BaseRadioGroup.Label>
            <div className="bg-white rounded-md -space-y-px">
                {options.map((option, index) => (
                    <BaseRadioGroup.Option
                        key={option.name}
                        value={option.id}
                        className={({ checked }) =>
                            classNames(
                                index === 0 ? 'rounded-tl-md rounded-tr-md' : '',
                                index === options.length - 1 ? 'rounded-bl-md rounded-br-md' : '',
                                checked ? 'bg-purple-100 border-purple-300 z-10' : 'border-gray-200',
                                'relative border p-4 flex cursor-pointer focus:outline-none'
                            )
                        }
                    >
                        {({ active, checked }) => (
                            <>
                                <span
                                    className={classNames(
                                        checked ? 'bg-purple-600 border-transparent' : 'bg-white border-gray-300',
                                        active ? 'ring-2 ring-offset-2 ring-purple-500' : '',
                                        'h-4 w-4 mt-0.5 cursor-pointer rounded-full border flex items-center justify-center'
                                    )}
                                    aria-hidden="true"
                                >
                                  <span className="rounded-full bg-white w-1.5 h-1.5" />
                                </span>
                                <div className="ml-3 flex flex-col">
                                    <BaseRadioGroup.Label
                                        as="span"
                                        className={classNames(checked ? 'text-purple-900' : 'text-gray-900', 'block text-sm font-medium')}
                                    >
                                        {option.icon && <i className={`fas fa-fw fa-${option.icon} mr-1.5 text-sm text-${option.colour}`} />}
                                        {option.name}
                                    </BaseRadioGroup.Label>
                                    <BaseRadioGroup.Description
                                        as="span"
                                        className={classNames(checked ? 'text-purple-700' : 'text-gray-500', 'block text-sm')}
                                    >
                                        {option.description}
                                    </BaseRadioGroup.Description>
                                </div>
                            </>
                        )}
                    </BaseRadioGroup.Option>
                ))}
            </div>
        </BaseRadioGroup>
    )
};

export default RadioGroup;
