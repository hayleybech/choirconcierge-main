import React from 'react';
import CheckboxWithLabel from './CheckboxWithLabel';
import classNames from '../../classNames';
import Label from './Label';
import CheckboxInput from './CheckboxInput';

const CheckboxGroup = ({ name, options, value, updateFn }) => (
	<div className="mt-4 grid grid-cols-2 md:flex md:flex-wrap">
		{options.map((option, key) => (
			<React.Fragment key={key}>
				<CheckboxWithLabel
					label={option.name}
					id={`${name}_${option.id}`}
					name={`${name}[]`}
					value={option.id}
					checked={value.includes(option.id)}
					onChange={e =>
						updateFn(e.target.checked ? addToArray(option.id, value) : deleteFromArray(option.id, value))
					}
					className="mr-8 mb-4"
				/>
			</React.Fragment>
		))}
	</div>
);

export default CheckboxGroup;

function addToArray(item, array) {
	return [...new Set(array).add(item)];
}

function deleteFromArray(item, array) {
	let set = new Set(array);
	set.delete(item);
	return [...set];
}

export const FancyCheckboxGroup = ({ name, options, value, updateFn, onChange, vertical, disabled }) => (
	<div className={classNames('mt-4 rounded-md bg-white flex', vertical ? 'flex-col' : 'flex-row')}>
		{options.map((option, key) => {
			const checked = value.includes(option.id);
			return (
				<React.Fragment key={key}>
					<label
						htmlFor={`${name}_${option.id}`}
						className={classNames(
							'relative border p-4 flex cursor-pointer focus:outline-none grow items-center text-sm gap-3',
							vertical && key === 0 && 'rounded-tl-md rounded-tr-md',
							vertical && key === options.length - 1 && 'rounded-bl-md rounded-br-md',
							!vertical && key === 0 && 'rounded-tl-md rounded-bl-md',
							!vertical && key === options.length - 1 && 'rounded-tr-md rounded-br-md',
							checked ? 'bg-purple-100 border-purple-300 z-10' : 'border-gray-300'
						)}
					>
						<div className="flex items-center h-5">
							<CheckboxInput
								id={`${name}_${option.id}`}
								name={`${name}[]`}
								value={option.id}
								checked={checked}
								onChange={e => {
									if(disabled) return;

									if (onChange) {
										onChange(option.id);
										return;
									}
									updateFn(
										e.target.checked
											? addToArray(option.id, value)
											: deleteFromArray(option.id, value)
									);
								}}
								disabled={disabled}
							/>
						</div>
						<div className="flex justify-between w-full items-center">
							<div className={classNames(
								'text-sm font-medium',
								checked ? 'text-purple-900' : 'text-gray-900',
								disabled && 'text-opacity-50'
							)}>{option.name}</div>
							<div
								className={classNames(
									'text-xs',
									checked ? 'text-purple-700' : 'text-gray-500',
									disabled && 'text-opacity-50'
								)}
							>
								{option.description}
							</div>
						</div>
					</label>
				</React.Fragment>
			);
		})}
	</div>
);
