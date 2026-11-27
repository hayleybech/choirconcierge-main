import {cva} from 'class-variance-authority';
import { twcx } from '../../classNames';

type ButtonIntent = 'primary' | 'secondary' | 'selected' | 'danger' | 'warning' | 'success';
type ButtonWeight = 'solid' | 'outline' | 'clear';
type ButtonSize = 'lg' | 'md' | 'sm' | 'xs';
type ButtonTheme = 'light' | 'dark';

const buttonStylesV2 = cva(
	'inline-flex justify-center items-center border font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 group cursor-pointer transition ease-in-out duration-150',
	{
		variants: {
			intent: { primary: '', secondary: '', selected: '', danger: '', warning: '', success: '' },
			weight: {
				solid: 'border-transparent',
				outline: 'bg-transparent',
				clear: 'border-transparent',
			},
			size: {
				lg: 'py-2 px-4 text-md rounded-md gap-x-1.5',
				md: 'py-1.5 px-3 text-sm rounded gap-x-1.5',
				sm: 'py-1 px-1.5 text-xs rounded gap-x-1',
				xs: 'py-0.5 px-1 text-xs rounded gap-x-1',
			},
			theme: { light: '', dark: '' },
			disabled: {
				false: null,
				true: 'opacity-50 cursor-not-allowed pointer-events-none',
			},
		},
		compoundVariants: [
			// Light Theme
			{
				intent: 'primary',
				weight: 'solid',
				theme: 'light',
				class: 'text-white bg-purple-600 hover:bg-purple-700',
			},
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
				class: 'bg-white border-gray-200 text-gray-600 hover:bg-gray-100 [&>i]:text-gray-500',
			},
			{
				intent: 'secondary',
				weight: 'clear',
				theme: 'light',
				class: 'text-gray-600 hover:text-gray-600 hover:bg-gray-100 [&>i]:text-gray-500',
			},
			{
				intent: 'selected',
				weight: 'solid',
				theme: 'light',
				class: 'bg-purple-100 border-purple-200 text-purple-700 hover:bg-purple-200',
			},
			{
				intent: 'selected',
				weight: 'outline',
				theme: 'light',
				class: 'bg-purple-50 border-purple-200 text-purple-700 hover:bg-purple-100 [&>i]:text-purple-600',
			},
			{
				intent: 'selected',
				weight: 'clear',
				theme: 'light',
				class: 'text-purple-700 hover:text-purple-800 hover:bg-purple-50 [&>i]:text-purple-600',
			},
			{
				intent: 'danger',
				weight: 'solid',
				theme: 'light',
				class: 'bg-red-600 text-white hover:bg-red-700 hover:bg-red-100',
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
			{ intent: 'warning', weight: 'solid', theme: 'light', class: 'bg-amber-600 text-white hover:bg-amber-700' },
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
			{
				intent: 'success',
				weight: 'solid',
				theme: 'light',
				class: ' text-white bg-emerald-600 hover:bg-emerald-700',
			},
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
			{
				intent: 'selected',
				weight: 'solid',
				theme: 'dark',
				class: 'bg-gray-700 border-purple-300 text-purple-100 hover:bg-purple-500/20',
			},
			{
				intent: 'selected',
				weight: 'outline',
				theme: 'dark',
				class: 'bg-gray-800 border-purple-300 text-purple-200 hover:bg-purple-500/20 [&>i]:text-purple-200',
			},
			{
				intent: 'selected',
				weight: 'clear',
				theme: 'dark',
				class: 'text-purple-200 hover:text-purple-100 hover:bg-purple-500/20 [&>i]:text-purple-200',
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
	twcx(buttonStylesV2({
		intent,
		weight: overrideWeight(intent, weight),
		size,
		theme,
		disabled,
	}), extra);

export default buttonStyles;

const overrideWeight = (intent : ButtonIntent, weight: ButtonWeight) => {
	if(intent === 'secondary' && weight === 'solid') {
		return 'outline';
	}

	if (intent === 'selected' && weight === 'solid') {
		return 'outline';
	}

	if (intent === 'primary' && weight === 'outline') {
		return 'solid';
	}

	return weight;
}