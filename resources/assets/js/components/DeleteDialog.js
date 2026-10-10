import React from 'react';
import Dialog from "./Dialog";

const DeleteDialog = ({ title, url, isOpen, setIsOpen, children }) => (
	<Dialog
		title={title}
		okLabel="Delete"
		okUrl={url}
		okColour="danger"
		okMethod="delete"
		isOpen={isOpen}
		setIsOpen={setIsOpen}
	>
		{children}
	</Dialog>
);

export default DeleteDialog;