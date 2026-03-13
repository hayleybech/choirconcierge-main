import React from 'react';
import { RadioGroup as BaseRadioGroup } from '@headlessui/react'
import classNames from "../../classNames";
import Icon from "../Icon";

const RadioGroup = ({
	label,
	options,
	selected,
	setSelected,
	disabled,
	vertical,
	contentVertical = true,
	size = 'md',
}) => (
	<BaseRadioGroup value={selected} onChange={setSelected}>
		<BaseRadioGroup.Label>{label}</BaseRadioGroup.Label>
		<div
			className={classNames(
				'bg-white rounded-md -space-y-px flex text-left',
				vertical ? 'flex-col' : 'flex-col md:flex-row',
				size === 'md' && 'mt-1',
			)}
		>
			{options.map((option, index) => (
				<BaseRadioGroup.Option
					key={option.id || option.name}
					value={option.id}
					disabled={disabled ?? option.disabled ?? false}
					className={({ checked }) =>
						classNames(
							'relative border flex cursor-pointer focus:outline-none grow items-center',
							vertical && index === 0 && 'rounded-tl-md rounded-tr-md',
							vertical && index === options.length - 1 && 'rounded-bl-md rounded-br-md',
							!vertical && index === 0 && 'rounded-tl-md rounded-bl-md',
							!vertical && index === options.length - 1 && 'rounded-tr-md rounded-br-md',
							checked ? 'bg-purple-100 border-purple-300 z-10' : 'border-gray-300',
							size === 'xs' && 'p-1.5',
							size === 'sm' && 'p-2',
							size === 'md' && 'p-4',
						)
					}
				>
					{({ active, checked }) => (
						<>
							<span
								className={classNames(
									'h-4 w-4 mt-0.5 cursor-pointer rounded-full border flex items-center justify-center shrink-0',
									checked ? 'bg-purple-600 border-transparent' : 'bg-white border-gray-300',
									active && 'ring-2 ring-offset-2 ring-purple-500'
								)}
								aria-hidden="true"
							>
								<span className="rounded-full bg-white w-1.5 h-1.5 shrink-0" />
							</span>

							{option.icon && (
								<Icon
									icon={option.icon}
									mr
									className={classNames(
										'text-lg ml-3',
										option.textColour ?? '',
										option.colour ?? '',
										checked && !option.colour && 'text-purple-700',
										!checked && !option.colour && 'text-gray-900',
										(disabled || option.disabled) && 'text-opacity-50',
										size === 'xs' ? 'text-base' : 'text-lg',
									)}
								/>
							)}

							<div
								className={classNames(
									'flex gap-1.5 w-full',
									contentVertical ? 'flex-col' : 'flex-row items-center justify-between',
									size === 'xs' && 'ml-0',
									size === 'sm' && 'ml-0',
									size === 'md' && 'ml-3',
								)}
							>
								<BaseRadioGroup.Label
									as="span"
									className={classNames(
										'block font-medium',
										checked ? 'text-purple-900' : 'text-gray-900',

										(disabled || option.disabled) && 'text-opacity-50',
										size === 'xs' ? 'text-xs' : 'text-sm',
									)}
								>
									{option.name}
								</BaseRadioGroup.Label>
								{option.description && (
									<BaseRadioGroup.Description
										as="div"
										className={classNames(
											'block',
											checked ? 'text-purple-700' : 'text-gray-500',
											(disabled || option.disabled) && 'text-opacity-50',
											size === 'xs' ? 'text-xs' : 'text-sm',
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
