import React from 'react';

export const voicePartColourClasses = {
    slate: 'bg-slate-500/75',
    stone: 'bg-stone-500/75',
    gray: 'bg-gray-500/75',
    red: 'bg-red-500/75',
    orange: 'bg-orange-500/75',
    amber: 'bg-amber-500/75',
    yellow: 'bg-yellow-500/75',
    lime: 'bg-lime-500/75',
    green: 'bg-green-500/75',
    emerald: 'bg-emerald-500/75',
    teal: 'bg-teal-500/75',
    cyan: 'bg-cyan-500/75',
    sky: 'bg-sky-500/75',
    blue: 'bg-blue-500/75',
    indigo: 'bg-indigo-500/75',
    violet: 'bg-violet-500/75',
    fuchsia: 'bg-fuchsia-500/75',
    pink: 'bg-pink-500/75',
    rose: 'bg-rose-500/75',
};

export const voicePartTextColourClasses = {
    slate: 'text-slate-500',
    stone: 'text-stone-500',
    gray: 'text-gray-500',
    red: 'text-red-500',
    orange: 'text-orange-500',
    amber: 'text-amber-500',
    yellow: 'text-yellow-500',
    lime: 'text-lime-500',
    green: 'text-green-500',
    emerald: 'text-emerald-500',
    teal: 'text-teal-500',
    cyan: 'text-cyan-500',
    sky: 'text-sky-500',
    blue: 'text-blue-500',
    indigo: 'text-indigo-500',
    violet: 'text-violet-500',
    fuchsia: 'text-fuchsia-500',
    pink: 'text-pink-500',
    rose: 'text-rose-500',
};

const VoicePartTag = ({ title, colour }) => (
    <span
        className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium text-white ${
            voicePartColourClasses[colour] ?? voicePartColourClasses.gray
        }`}
    >
        <svg className="-ml-0.5 mr-1.5 h-2 w-2 text-white" fill="currentColor" viewBox="0 0 8 8">
          <circle cx={4} cy={4} r={3} />
        </svg>
        { title }
    </span>
);

export default VoicePartTag;
