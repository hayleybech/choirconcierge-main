import React from 'react';
import classNames from '../../classNames';

const CheckboxInput = React.forwardRef(({ id, name, value, checked, onChange, className, disabled, ...props }, ref) => (
	<input
		id={id}
		name={name}
		value={value}
		checked={checked}
		onChange={onChange}
		type="checkbox"
		className={classNames(
			'focus:ring-purple-500 h-4 w-4  border-gray-300 rounded indeterminate:bg-gray-600  ',
			disabled ? 'bg-gray-200 text-purple-400 ' : 'text-purple-600',
			className
		)}
		ref={ref}
		{...props}
	/>
));

export default CheckboxInput;
