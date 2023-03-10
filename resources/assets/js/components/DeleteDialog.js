import React from 'react';
import Dialog from "./Dialog";

const DeleteDialog = ({ title, url, isOpen, setIsOpen, children }) => (
	<Dialog
		title={title}
		okLabel="Delete"
		okUrl={url}
		okVariant="danger-solid"
		okMethod="delete"
		isOpen={isOpen}
		setIsOpen={setIsOpen}
	>
		{children}
	</Dialog>
);

export default DeleteDialog;