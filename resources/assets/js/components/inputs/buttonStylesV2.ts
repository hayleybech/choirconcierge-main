import {cva} from 'class-variance-authority';

type ButtonColor = 'primary' | 'secondary' | 'danger' | 'warning' | 'success';
type ButtonWeight = 'solid' | 'outline' | 'clear';
type ButtonSize = 'lg' | 'md' | 'sm';
type ButtonTheme = 'light' | 'dark';

const buttonStylesV2 = cva(
	'inline-flex justify-center items-center gap-x-1.5 border-2 font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 group hover:cursor-pointer',
	{
		variants: {
			color: {primary: '', secondary: '', danger: '', warning: '', success: ''},
			weight: {
				solid: 'border-transparent text-white',
				outline: 'bg-transparent',
				clear: 'border-transparent',
			},
			size: {
				lg: 'py-2.5 px-5 text-lg rounded-md',
				md: 'py-2 px-4 text-md rounded-md',
				sm: 'py-1.5 px-3 text-sm rounded',
			},
			theme: {light: '', dark: ''},
		},
		compoundVariants: [
			// Light Theme
			{color: 'primary', weight: 'solid', theme: 'light', class: 'bg-purple-600 hover:bg-purple-700'},
			{color: 'primary', weight: 'outline', theme: 'light', class: 'border-purple-300 text-purple-600 hover:bg-purple-100'},
			{color: 'primary', weight: 'clear', theme: 'light', class: 'text-purple-500 hover:text-purple-700'},
			{color: 'secondary', weight: 'solid', theme: 'light', class: 'bg-gray-400 border-gray-500 text-gray-900 hover:bg-gray-500'},
			{color: 'secondary', weight: 'outline', theme: 'light', class: 'border-gray-300 text-gray-700 hover:bg-gray-100'},
			{color: 'secondary', weight: 'clear', theme: 'light', class: 'text-gray-700 hover:text-gray-500'},
			{color: 'danger', weight: 'solid', theme: 'light', class: 'bg-red-600 hover:bg-red-700'},
			{color: 'danger', weight: 'outline', theme: 'light', class: 'border-red-300 text-red-500 hover:bg-red-100'},
			{color: 'danger', weight: 'clear', theme: 'light', class: 'text-red-500 hover:text-red-700'},
			{color: 'warning', weight: 'solid', theme: 'light', class: 'bg-amber-600 hover:bg-amber-700'},
			{color: 'warning', weight: 'outline', theme: 'light', class: 'border-amber-300 text-amber-500 hover:bg-amber-100'},
			{color: 'warning', weight: 'clear', theme: 'light', class: 'text-amber-500 hover:text-amber-700'},
			{color: 'success', weight: 'solid', theme: 'light', class: 'bg-emerald-600 hover:bg-emerald-700'},
			{color: 'success', weight: 'outline', theme: 'light', class: 'border-emerald-300 text-emerald-500 hover:bg-emerald-100'},
			{color: 'success', weight: 'clear', theme: 'light', class: 'text-emerald-500 hover:text-emerald-700'},
			// Dark Theme
			{color: 'primary', weight: 'solid', theme: 'dark', class: 'bg-purple-600 hover:bg-purple-500'},
			{color: 'primary', weight: 'outline', theme: 'dark', class: 'bg-gray-800 border-purple-400 text-purple-400 hover:bg-purple-500/30'},
			{color: 'primary', weight: 'clear', theme: 'dark', class: 'text-purple-400 hover:text-purple-300'},
			{color: 'secondary', weight: 'clear', theme: 'dark', class: 'text-gray-100 hover:text-gray-300'},
			{color: 'secondary', weight: 'solid', theme: 'dark', class: 'bg-gray-700 border-gray-600 text-gray-100 hover:bg-gray-600'},
			{color: 'secondary', weight: 'outline', theme: 'dark', class: 'bg-gray-800 border-gray-300 text-gray-100 hover:bg-gray-700'},
			{color: 'danger', weight: 'solid', theme: 'dark', class: 'bg-red-600 hover:bg-red-500'},
			{color: 'danger', weight: 'outline', theme: 'dark', class: 'bg-gray-800 border-red-500 text-red-500 hover:bg-red-600/30'},
			{color: 'danger', weight: 'clear', theme: 'dark', class: 'text-red-500 hover:text-red-300'},
			{color: 'warning', weight: 'solid', theme: 'dark', class: 'bg-amber-500 hover:bg-amber-400'},
			{color: 'warning', weight: 'outline', theme: 'dark', class: 'bg-gray-800 border-amber-500 text-amber-500 hover:bg-amber-700/30'},
			{color: 'warning', weight: 'clear', theme: 'dark', class: 'text-amber-500 hover:text-amber-300'},
			{color: 'success', weight: 'solid', theme: 'dark', class: 'bg-emerald-500 hover:bg-emerald-400'},
			{color: 'success', weight: 'outline', theme: 'dark', class: 'bg-gray-800 border-emerald-500 text-emerald-500 hover:bg-emerald-700/30'},
			{color: 'success', weight: 'clear', theme: 'dark', class: 'text-emerald-500 hover:text-emerald-300'},
		],
		defaultVariants: {color: 'secondary', weight: 'solid', size: 'md', theme: 'light'},
	},
);

const buttonStyles = (color: ButtonColor = 'secondary', weight: ButtonWeight = 'solid', size: ButtonSize = 'md', theme: ButtonTheme = 'light', disabled = false, extra = '') =>
	buttonStylesV2({color, weight, size, theme, className: `${disabled ? 'opacity-50 cursor-not-allowed pointer-events-none' : ''} ${extra}`});

export default buttonStyles;