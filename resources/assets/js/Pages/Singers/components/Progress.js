import React from "react";

export const Progress = ({ value, max, min }) => (
    <div className="flex items-center text-xs">
        {min}
        <div className="grow mx-2">
            <div className="h-5 bg-purple-100 border border-purple-300 rounded-sm overflow-hidden">
                <div className="bg-purple-600 h-full flex justify-center items-center text-white" style={{ width: `${value / max * 100}%`}}>
                    {value}
                </div>
            </div>
        </div>
        {max}
    </div>
);