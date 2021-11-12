import React from 'react';

const Form = ({ onSubmit, children }) => (
	<form className="space-y-8 divide-y divide-gray-200" onSubmit={onSubmit}>
		{children}
	</form>
);

export default Form;