import React from 'react';
import Dialog from '../Dialog';
import Icon from '../Icon';

const AttendanceInstructionsDialog = ({ isOpen, setIsOpen }) => (
	<Dialog
		title={
			<span className="flex items-center gap-1.5">
				<Icon icon="info-circle" type="regular" className="text-gray-800" />
				Attendance Tracking Instructions
			</span>
		}
		isOpen={isOpen}
		setIsOpen={setIsOpen}
		icon={null}
		showCancelButton={false}
	>
		<div className="text-left">
			<p className="mb-2">We offer three ways to track attendance:</p>
			<ul className="mb-4 ml-4 list-inside list-disc [&>li]:mb-1">
				<li>
					<strong>Manual</strong> attendance tracking on this page
				</li>
				<li>
					<strong>Kiosk</strong> check-in for shared device use
				</li>
				<li>
					<strong>QR Code</strong> check-in for individual use
				</li>
			</ul>
			<p className="mb-2">
				Both device check-in pages automatically mark singers as late after the call time, or absent 20 minutes
				later.
			</p>
			<p className="mb-2">
				If attendance was partially recorded during the event (either manually here or using the kiosk), remaining
				singers will automatically be marked absent once the event ends.
			</p>
			<p>The check-in pages also send an attendance report after each event.</p>
		</div>
	</Dialog>
);

export default AttendanceInstructionsDialog;
