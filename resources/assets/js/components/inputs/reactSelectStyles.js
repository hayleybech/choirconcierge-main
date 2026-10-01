const reactSelectStyles = {
	option: (base, state) => ({
		...base,
		backgroundColor: state.isSelected
			? 'var(--color-purple-400)'
			: state.isFocused
			? 'var(--color-purple-100)'
			: base.backgroundColor,
		color: state.isSelected ? 'white' : base.color,
	}),
};

export default reactSelectStyles;