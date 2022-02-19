import React from 'react';
import Toast from "./Toast";

const ToastSuccess = ({ show, close, title, children }) => (
	<Toast
		show={show}
		close={close}
		title={title}
		icon="check-circle"
		iconClass="text-green-400"
	>
		{children}
	</Toast>
);

export default ToastSuccess;