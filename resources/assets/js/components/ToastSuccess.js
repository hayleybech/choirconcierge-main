import React from 'react';
import Toast from "./Toast";

const ToastSuccess = ({ show, close, title, body }) => (
	<Toast
		show={show}
		close={close}
		title={title}
		body={body}
		icon="check-circle"
		iconClass="text-green-400"
	/>
);

export default ToastSuccess;