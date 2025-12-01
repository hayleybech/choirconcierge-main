import React from 'react';
import {Range} from "react-range";

const RangeInput = ({ updateFn, values, ...otherProps }) => (
    <Range
        {...otherProps}
        values={values}
        onChange={(values) => updateFn( values )}
        renderTrack={({ props, children }) => (
            <div
                {...props}
                style={props.style}
                className="h-1.5 w-full bg-gray-200"
            >
                {children}
            </div>
        )}
        renderThumb={({ props }) => (
            <div
                {...props}
                style={props.style}
                className="h-4 w-4 bg-purple-500 rounded-full"
            />
        )}
    />
);

export default RangeInput;