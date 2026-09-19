import React from 'react';
import classNames from '../../classNames';

const TextInput = ({
	name,
	type = 'text',
	value,
	updateFn,
	hasErrors,
	disabled,
	size = 'md',
	className = '',
	wrapperClasses = '',
	...otherProps
}) => (
	<div className={`${size === 'md' && 'mt-1'} ${wrapperClasses}`}>
		<input
			type={type}
			name={name}
			id={name}
			className={classNames(
				'' + 'shadow-sm focus:outline-none block w-full rounded-md',
				hasErrors && 'border-red-300 text-red-900 placeholder-red-300 focus:ring-red-500 focus:border-red-500',
				disabled && 'bg-gray-200 text-gray-700 border-gray-300',
				!hasErrors && !disabled && 'border-gray-300 focus:ring-purple-500 focus:border-purple-500',
				size === 'xs' && 'text-xs py-0.5 px-2 rounded',
				size === 'sm' && 'text-sm py-1 px-2 rounded',
				size === 'md' && 'text-base p-2 rounded-md sm:text-sm',

				className
			)}
			value={value}
			onChange={e => updateFn(e.target.value)}
			disabled={disabled}
			{...otherProps}
		/>
	</div>
);

export default TextInput;