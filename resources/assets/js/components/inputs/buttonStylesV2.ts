import {cva} from 'class-variance-authority';

type ButtonIntent = 'primary' | 'secondary' | 'danger' | 'warning' | 'success';
type ButtonWeight = 'solid' | 'outline' | 'clear';
type ButtonSize = 'lg' | 'md' | 'sm';
type ButtonTheme = 'light' | 'dark';

const buttonStylesV2 = cva(
	'inline-flex justify-center items-center gap-x-1.5 border font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 group hover:cursor-pointer',
	{
		variants: {
			intent: { primary: '', secondary: '', danger: '', warning: '', success: '' },
			weight: {
				solid: 'border-transparent text-white',
				outline: 'bg-transparent',
				clear: 'border-transparent',
			},
			size: {
				lg: 'py-2 px-4 text-md rounded-md',
				md: 'py-1.5 px-3 text-sm rounded',
				sm: 'py-1 px-1.5 text-xs rounded',
			},
			theme: { light: '', dark: '' },
		},
		compoundVariants: [
			// Light Theme
			{ intent: 'primary', weight: 'solid', theme: 'light', class: 'bg-purple-600 hover:bg-purple-700' },
			{
				intent: 'primary',
				weight: 'outline',
				theme: 'light',
				class: 'bg-white border-purple-300 text-purple-600 hover:bg-purple-100',
			},
			{
				intent: 'primary',
				weight: 'clear',
				theme: 'light',
				class: 'text-purple-500 hover:text-purple-700 hover:bg-purple-100',
			},
			{
				intent: 'secondary',
				weight: 'solid',
				theme: 'light',
				class: 'bg-gray-400 border-gray-500 text-gray-900 hover:bg-gray-500',
			},
			{
				intent: 'secondary',
				weight: 'outline',
				theme: 'light',
				class: 'bg-white border-gray-300 text-gray-700 hover:bg-gray-100',
			},
			{
				intent: 'secondary',
				weight: 'clear',
				theme: 'light',
				class: 'text-gray-700 hover:text-gray-600 hover:bg-gray-100',
			},
			{
				intent: 'danger',
				weight: 'solid',
				theme: 'light',
				class: 'bg-red-600 hover:bg-red-700 hover:bg-red-100',
			},
			{
				intent: 'danger',
				weight: 'outline',
				theme: 'light',
				class: 'bg-white border-red-300 text-red-500 hover:bg-red-100',
			},
			{
				intent: 'danger',
				weight: 'clear',
				theme: 'light',
				class: 'text-red-500 hover:text-red-700 hover:bg-red-100',
			},
			{ intent: 'warning', weight: 'solid', theme: 'light', class: 'bg-amber-600 hover:bg-amber-700' },
			{
				intent: 'warning',
				weight: 'outline',
				theme: 'light',
				class: 'bg-white border-amber-300 text-amber-500 hover:bg-amber-100',
			},
			{
				intent: 'warning',
				weight: 'clear',
				theme: 'light',
				class: 'text-amber-500 hover:text-amber-700 hover:bg-amber-100',
			},
			{ intent: 'success', weight: 'solid', theme: 'light', class: 'bg-emerald-600 hover:bg-emerald-700' },
			{
				intent: 'success',
				weight: 'outline',
				theme: 'light',
				class: 'bg-white border-emerald-300 text-emerald-500 hover:bg-emerald-100',
			},
			{
				intent: 'success',
				weight: 'clear',
				theme: 'light',
				class: 'text-emerald-500 hover:text-emerald-700 hover:bg-emerald-100',
			},
			// Dark Theme
			{ intent: 'primary', weight: 'solid', theme: 'dark', class: 'bg-purple-600 hover:bg-purple-500' },
			{
				intent: 'primary',
				weight: 'outline',
				theme: 'dark',
				class: 'bg-gray-800 border-purple-400 text-purple-400 hover:bg-purple-500/30',
			},
			{
				intent: 'primary',
				weight: 'clear',
				theme: 'dark',
				class: 'text-purple-400 hover:text-purple-300 hover:bg-purple-500/30',
			},
			{
				intent: 'secondary',
				weight: 'clear',
				theme: 'dark',
				class: 'text-gray-100 hover:text-gray-200 hover:bg-gray-600',
			},
			{
				intent: 'secondary',
				weight: 'solid',
				theme: 'dark',
				class: 'bg-gray-700 border-gray-600 text-gray-100 hover:bg-gray-600',
			},
			{
				intent: 'secondary',
				weight: 'outline',
				theme: 'dark',
				class: 'bg-gray-800 border-gray-300 text-gray-100 hover:bg-gray-700',
			},
			{ intent: 'danger', weight: 'solid', theme: 'dark', class: 'bg-red-600 hover:bg-red-500' },
			{
				intent: 'danger',
				weight: 'outline',
				theme: 'dark',
				class: 'bg-gray-800 border-red-500 text-red-500 hover:bg-red-600/30',
			},
			{
				intent: 'danger',
				weight: 'clear',
				theme: 'dark',
				class: 'text-red-500 hover:text-red-300 hover:bg-red-600/30',
			},
			{ intent: 'warning', weight: 'solid', theme: 'dark', class: 'bg-amber-500 hover:bg-amber-400' },
			{
				intent: 'warning',
				weight: 'outline',
				theme: 'dark',
				class: 'bg-gray-800 border-amber-500 text-amber-500 hover:bg-amber-700/30',
			},
			{
				intent: 'warning',
				weight: 'clear',
				theme: 'dark',
				class: 'text-amber-500 hover:text-amber-300 hover:bg-amber-700/30',
			},
			{ intent: 'success', weight: 'solid', theme: 'dark', class: 'bg-emerald-500 hover:bg-emerald-400' },
			{
				intent: 'success',
				weight: 'outline',
				theme: 'dark',
				class: 'bg-gray-800 border-emerald-500 text-emerald-500 hover:bg-emerald-700/30',
			},
			{
				intent: 'success',
				weight: 'clear',
				theme: 'dark',
				class: 'text-emerald-500 hover:text-emerald-300 hover:bg-emerald-700/30',
			},
		],
		defaultVariants: { intent: 'secondary', weight: 'solid', size: 'md', theme: 'light' },
	}
);

const buttonStyles = (intent: ButtonIntent = 'secondary', weight: ButtonWeight = 'solid', size: ButtonSize = 'md', theme: ButtonTheme = 'light', disabled = false, extra = '') =>
	buttonStylesV2({
		intent,
		weight: overrideWeight(intent, weight),
		size,
		theme,
		className: `${disabled ? 'opacity-50 cursor-not-allowed pointer-events-none' : ''} ${extra}`,
	});

export default buttonStyles;

const overrideWeight = (intent : ButtonIntent, weight: ButtonWeight) => {
	if(intent === 'secondary' && weight === 'solid') {
		return 'outline';
	}

	if (intent === 'primary' && weight === 'outline') {
		return 'solid';
	}

	return weight;
}