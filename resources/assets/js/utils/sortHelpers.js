export const handleNameSort = ({ data, setData }) => {
	const nextState = {
		// (full-name, asc) -> (last-name-first, asc)
		'full-name,asc': { sort: 'last-name-first', sortDir: 'asc' },
		// (last-name-first, asc) -> (full-name, desc)
		'last-name-first,asc': { sort: 'full-name', sortDir: 'desc' },
		// (full-name, desc) -> (last-name-first, desc)
		'full-name,desc': { sort: 'last-name-first', sortDir: 'desc' },
		// (last-name-first, desc) -> (full-name, asc)
		'last-name-first,desc': { sort: 'full-name', sortDir: 'asc' },
	}[`${data.sort},${data.sortDir}`] || { sort: 'full-name', sortDir: 'asc' };

	setData(data => ({ ...data, ...nextState }));
};
