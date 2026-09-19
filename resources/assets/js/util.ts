export function nl2br(str: string, is_xhtml: boolean = true): string {
	// http://kevin.vanzonneveld.net
	// +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	// +   improved by: Philip Peterson
	// +   improved by: Onno Marsman
	// +   improved by: Atli Þór
	// +   bugfixed by: Onno Marsman
	// +      input by: Brett Zamir (http://brett-zamir.me)
	// +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	// +   improved by: Brett Zamir (http://brett-zamir.me)
	// +   improved by: Maximusya
	// *     example 1: nl2br('Kevin\nvan\nZonneveld');
	// *     returns 1: 'Kevin<br />\nvan<br />\nZonneveld'
	// *     example 2: nl2br("\nOne\nTwo\n\nThree\n", false);
	// *     returns 2: '<br>\nOne<br>\nTwo<br>\n<br>\nThree<br>\n'
	// *     example 3: nl2br("\nOne\nTwo\n\nThree\n", true);
	// *     returns 3: '<br />\nOne<br />\nTwo<br />\n<br />\nThree<br />\n'
	const breakTag = is_xhtml ? '<br ' + '/>' : '<br>'; // Adjust comment to avoid issue on phpjs.org display

	return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
}

export const plural = (word: string, qty: number = 2): string => {
	if (qty === 1) {
		return word;
	}

	if (word.endsWith('y')) {
		return word.slice(0, -1) + 'ies';
	}
	return word + 's';
};
