import React from 'react';
import CheckboxInput from "./CheckboxInput";
import Label from "./Label";
import classNames from "../../classNames";

const CheckboxWithLabel = ({ id, name, label, value, checked, onChange, className = '' }) => (
    <div className={classNames('relative flex items-start sm:col-span-6 gap-3', className)}>
        <div className="flex items-center h-5">
            <CheckboxInput id={id} name={name} value={value} checked={checked} onChange={onChange} />
        </div>
        <div className="text-sm">
            <Label label={label} forInput={id} />
        </div>
    </div>
);

export default CheckboxWithLabel;