import React from 'react';
import Toast from "./Toast";

const ToastError = ({ show, close, title = 'An error occurred', children }) => (
	<Toast
		show={show}
		close={close}
		title={title}
		titleClass="text-red-900 font-bold"
		icon="times-circle"
		iconClass="text-red-400"
	>
		{children}
	</Toast>
);

export default ToastError;