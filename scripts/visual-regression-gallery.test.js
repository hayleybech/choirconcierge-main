const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const test = require('node:test');
const vm = require('node:vm');

function createElement(dataset = {}, hidden = false) {
	return {
		dataset,
		hidden,
		listeners: {},
		value: '',
		addEventListener(event, listener) {
			this.listeners[event] = listener;
		},
	};
}

test('switches between one grid capture variant and the comparison view', () => {
	const gallery = createElement({ view: 'grid' });
	const galleryView = createElement();
	galleryView.value = 'grid';
	const gridVariantSelect = createElement();
	gridVariantSelect.value = 'latest';
	const baseline = createElement({ gridVariant: 'baseline' }, true);
	const latest = createElement({ gridVariant: 'latest' });
	const gridCaptures = createElement({}, false);
	const comparisonCaptures = createElement({}, true);
	const filter = createElement({ filter: 'module' });
	const card = createElement({ module: 'auth' });
	const selectors = {
		'[data-gallery]': gallery,
		'[data-gallery-view]': galleryView,
		'[data-grid-variant-select]': gridVariantSelect,
	};
	const document = {
		querySelector(selector) {
			return selectors[selector];
		},
		querySelectorAll(selector) {
			return {
				'[data-filter]': [filter],
				'[data-screenshot]': [card],
				'[data-grid-captures]': [gridCaptures],
				'[data-comparison-captures]': [comparisonCaptures],
				'[data-grid-variant]': [baseline, latest],
			}[selector];
		},
	};
	const viewPath = path.join(__dirname, '..', 'resources', 'views', 'visual-regression', 'gallery.blade.php');
	const view = fs.readFileSync(viewPath, 'utf8');
	const script = view.match(/<script>([\s\S]*?)<\/script>/);

	assert.ok(script, 'the gallery should contain its client-side controls');
	vm.runInNewContext(script[1], { document });

	gridVariantSelect.value = 'baseline';
	gridVariantSelect.listeners.change();
	assert.equal(baseline.hidden, false);
	assert.equal(latest.hidden, true);
	assert.equal(gridCaptures.hidden, false);
	assert.equal(comparisonCaptures.hidden, true);

	galleryView.value = 'compare';
	galleryView.listeners.change();
	assert.equal(gallery.dataset.view, 'compare');
	assert.equal(gridCaptures.hidden, true);
	assert.equal(comparisonCaptures.hidden, false);

	filter.value = 'songs';
	filter.listeners.change();
	assert.equal(card.hidden, true);
});
