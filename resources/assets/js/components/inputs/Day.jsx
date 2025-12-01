import React from 'react';
import classNames from '../../classNames';
import DayPicker from "react-day-picker";
import { Popover, Transition } from "@headlessui/react";
import 'react-day-picker/lib/style.css';
import {DateTime} from "luxon";
import Icon from "../Icon";

const DayInput = ({ name, value, updateFn, hasErrors, min, max }) => (
    <div className="mt-1 relative">
        <input
            id={name}
            name={name}
            type="date"
            value={value}
            onChange={e => updateFn(e.target.value)}
            className={classNames('' +
                'shadow-sm focus:outline-none block w-full sm:text-sm rounded-md',
                hasErrors
                    ? 'border-red-300 text-red-900 placeholder-red-300 focus:ring-red-500 focus:border-red-500'
                    : 'border-gray-300 focus:ring-purple-500 focus:border-purple-500'
            )}
            min={min}
            max={max}
        />
    </div>
);

export default DayInput;