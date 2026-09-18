import React from 'react';
import QRCode from 'react-qr-code';
import Dialog from '../Dialog';

const SelfCheckInDialog = ({ individualCheckInUrl, isOpen, setIsOpen }) => (
	<Dialog title="Individual Check-In Link" isOpen={isOpen} setIsOpen={setIsOpen} icon={null}>
		<div className="w-full">
			<p className="font-bold mb-2">Let singers check themselves in!</p>
			<p className="mb-2">
				They can scan this QR code while logged in to gain temporary access to the check-in page.
			</p>

			<div className="mb-2 flex justify-center">
				<QRCode value={individualCheckInUrl} />
			</div>
			<p className="break-all text-xs">{individualCheckInUrl}</p>
		</div>
	</Dialog>
);

export default SelfCheckInDialog;
