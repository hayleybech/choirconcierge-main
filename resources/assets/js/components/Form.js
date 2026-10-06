import React from 'react';

const Form = ({ onSubmit, children }) => (
	<form className="space-y-8" onSubmit={onSubmit}>
		{children}
	</form>
);

export default Form;