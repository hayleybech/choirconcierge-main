import React from 'react';
import Toast from "./Toast";

const ToastError = ({ show, close, title, errors }) => (
	<Toast
		show={show}
		close={close}
		title={title}
		icon="times-circle"
		iconClass="text-red-400"
	>
		<p className="text-red-500 font-bold">The following errors occurred:</p>
		<ul className="text-red-500">
			{Object.values(errors).map((error) => <li>{error}</li>)}
		</ul>
	</Toast>
);

export default ToastError;