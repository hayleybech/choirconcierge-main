const ColourPicker = ({ value, updateFn }) => {
    // the bg and text props are here to force tailwind to build these colour values
    const tailwindColours = {
        slate: {
            bg: 'bg-slate-500',
            text: 'text-slate-500',
        },
        stone: {
            bg: 'bg-stone-500',
            text: 'text-stone-500',
        },
        red: {
            bg: 'bg-red-500',
            text: 'text-red-500',
        },
        orange: {
            bg: 'bg-orange-500',
            text: 'text-orange-500',
        },
        amber: {
            bg: 'bg-amber-500',
            text: 'text-amber-500',
        },
        yellow: {
            bg: 'bg-yellow-500',
            text: 'text-yellow-500',
        },
        lime: {
            bg: 'bg-lime-500',
            text: 'text-lime-500',
        },
        green: {
            bg: 'bg-green-500',
            text: 'text-green-500',
        },
        emerald: {
            bg: 'bg-emerald-500',
            text: 'text-emerald-500',
        },
        teal: {
            bg: 'bg-teal-500',
            text: 'text-teal-500',
        },
        cyan: {
            bg: 'bg-cyan-500',
            text: 'text-cyan-500',
        },
        sky: {
            bg: 'bg-sky-500',
            text: 'text-sky-500',
        },
        blue: {
            bg: 'bg-blue-500',
            text: 'text-blue-500',
        },
        indigo: {
            bg: 'bg-indigo-500',
            text: 'text-indigo-500',
        },
        violet: {
            bg: 'bg-violet-500',
            text: 'text-violet-500',
        },
        fuchsia: {
            bg: 'bg-fuchsia-500',
            text: 'text-fuchsia-500',
        },
        pink: {
            bg: 'bg-pink-500',
            text: 'text-pink-500',
        },
        rose: {
            bg: 'bg-rose-500',
            text: 'text-rose-500',
        },
    };

    return <div className="flex flex-wrap gap-2 mt-1">
        {Object.entries(tailwindColours).map(([colourName, colour]) => (
            <div
                className={`
                    h-8 w-8 rounded-md outline outline-2 outline-offset-2 cursor-pointer hover:opacity-75 focus:opacity-75
                    ${colour.bg}
                    ${value === colourName ? 'outline-purple-800' : 'outline-transparent'}
                `}
                onClick={() => updateFn(colourName)}
            />
        ))}
    </div>;
};

export default ColourPicker;