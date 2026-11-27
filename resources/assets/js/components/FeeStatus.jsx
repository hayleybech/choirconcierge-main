import classNames from "../classNames";
import Icon from "./Icon";
import React from "react";
import Tooltip from "./Tooltip";

const FeeStatus = ({ status, tooltip, compact = false }) => {
	const feeStatus = STATUSES[status];
	const tooltipContent = status === 'unknown' ? null : tooltip;

	return (
		<Tooltip content={tooltipContent}>
			<span
				className={classNames(compact ? 'text-xs' : 'text-sm', feeStatus.colour)}
			>
				<Icon icon={feeStatus.icon} mr className={classNames(compact ? 'text-xs' : '', feeStatus.colour)} />
				<span className="font-semibold truncate">{feeStatus.label}</span>
			</span>
		</Tooltip>
	);
};

export default FeeStatus;

const STATUSES = {
	paid: { label: 'Paid', icon: 'check-circle', colour: 'text-green-500' },
	'expires-soon': { label: 'Expires Soon', icon: 'exclamation-triangle', colour: 'text-orange-500' },
	expired: { label: 'Expired', icon: 'times-circle', colour: 'text-red-500' },
	unknown: { label: 'Unknown', icon: 'question-circle', colour: 'text-gray-500' },
}
