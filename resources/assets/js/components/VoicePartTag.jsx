import React from 'react';

 // Badge
const VoicePartTag = ({ title, colour }) => (
    <span
        className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium text-white bg-opacity-75 bg-${colour ?? 'gray'}-500`}
    >
        <svg className="-ml-0.5 mr-1.5 h-2 w-2 text-white" fill="currentColor" viewBox="0 0 8 8">
          <circle cx={4} cy={4} r={3} />
        </svg>
        { title }
    </span>
);

export default VoicePartTag;