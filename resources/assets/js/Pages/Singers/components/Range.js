import React from "react";

export const Range = ({ value, min, max, minLabel, maxLabel }) => (
    <div className="flex items-center text-xs">
        {minLabel}
        <div className="grow mx-2">
            <div className="h-1 bg-gray-200 rounded-sm flex items-center pr-3">
                <div className="w-full relative">
                    <div
                        className="bg-gray-600 h-3 w-3 flex justify-center items-center text-white relative rounded-full"
                        style={{ left: `${value / max * 100}%`}}
                    />
                </div>
            </div>
        </div>
        {maxLabel}
    </div>
);