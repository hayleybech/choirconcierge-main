import React from 'react';
import Toast from "./Toast";

const ToastError = ({ show, close, errors }) => (
	<Toast
		show={show}
		close={close}
		title="Form validation errors"
		titleClass="text-red-900 font-bold"
		icon="times-circle"
		iconClass="text-red-400"
	>
		<ul className="text-red-500 list-disc">
			{Object.values(errors).map((error) => <li key={error}>{error}</li>)}
		</ul>
	</Toast>
);

export default ToastError;