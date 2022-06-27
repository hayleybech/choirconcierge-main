import classNames from "../classNames";
import Icon from "./Icon";
import React from "react";

const FeeStatus = ({ isPaid }) => (
	<div className={classNames('mb-2 text-sm', isPaid ? 'text-green-500' : 'text-red-500')}>
		<Icon
			icon={isPaid ? 'check-circle' : 'times-circle'}
			mr
			className={isPaid ? 'text-green-500' : 'text-red-500'}
		/>
		<span className="font-semibold truncate">
            {isPaid ? 'Paid' : 'Expired'}
        </span>
	</div>
);

export default FeeStatus