import classNames from "../classNames";
import Icon from "./Icon";
import React from "react";

const FeeStatus = ({ status, tooltip }) => (
	<div className={classNames('text-sm', STATUSES[status].colour)} title={tooltip}>
		<Icon icon={STATUSES[status].icon} mr className={STATUSES[status].colour} />
		<span className="font-semibold truncate">
            {STATUSES[status].label}
        </span>
	</div>
);

export default FeeStatus;

const STATUSES = {
	paid: { label: 'Paid', icon: 'check-circle', colour: 'text-green-500' },
	'expires-soon': { label: 'Expires Soon', icon: 'exclamation-triangle', colour: 'text-orange-500' },
	expired: { label: 'Expired', icon: 'times-circle', colour: 'text-red-500' },
	unknown: { label: 'Unknown', icon: 'question-circle', colour: 'text-gray-500' },
}
