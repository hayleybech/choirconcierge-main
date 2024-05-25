import React from "react";

jest.mock('@inertiajs/react', () => {
	console.log('gloabl mock');
	const { forwardRef } = jest.requireActual('react');

	return {
		usePage: jest.fn(() => ({props: {}})),
		Link: forwardRef(({children, ...props}, ref) => <a ref={ref} {...props}>{children}</a>),
	};
});

// @todo confirm that this approach works
// Paste this to partially override in a single test
// usePage.mockImplementation(() => ({
//   props: {
//     tenant: {
//       id: 2,
//       logo_url: 'choir2.png',
//       name: 'Choir Number 2',
//     }
//   },
// }))