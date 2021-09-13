import React from 'react';

 // Badge
 // @todo Use tailwind colours for voice parts
 // span: bg-indigo-100 text-indigo-800
 // svg: text-indigo-400
const VoicePartTag = ({ title, colour }) => (
    <span
        className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-opacity-75"
        style={{ color: 'white', backgroundColor: colour ?? '#9095a0' }}
    >
        <svg className="-ml-0.5 mr-1.5 h-2 w-2" fill="currentColor" viewBox="0 0 8 8" style={{ color: 'white' }}>>
          <circle cx={4} cy={4} r={3} />
        </svg>
        { title }
    </span>
);

export default VoicePartTag;