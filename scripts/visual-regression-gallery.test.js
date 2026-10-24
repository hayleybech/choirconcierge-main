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
		open: false,
		src: '',
		alt: '',
		addEventListener(event, listener) {
			this.listeners[event] = listener;
		},
		querySelector(selector) {
			return this[selector];
		},
		showModal() {
			this.open = true;
		},
		close() {
			this.open = false;
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
	const imageZoomDialog = createElement();
	const zoomImage = createElement();
	const zoomCaption = createElement();
	imageZoomDialog['[data-zoom-image]'] = zoomImage;
	imageZoomDialog['[data-zoom-caption]'] = zoomCaption;
	const thumbnail = createElement();
	thumbnail.src = '/screenshots/auth-login/desktop/latest';
	thumbnail.alt = 'Latest capture for Auth/Login at Desktop';
	const zoomTrigger = createElement();
	zoomTrigger['img'] = thumbnail;
	const filter = createElement({ filter: 'module' });
	const card = createElement({ module: 'auth' });
	const selectors = {
		'[data-gallery]': gallery,
		'[data-gallery-view]': galleryView,
		'[data-grid-variant-select]': gridVariantSelect,
		'[data-image-zoom]': imageZoomDialog,
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
				'[data-zoom-trigger]': [zoomTrigger],
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

	zoomTrigger.listeners.click();
	assert.equal(imageZoomDialog.open, true);
	assert.equal(zoomImage.src, thumbnail.src);
	assert.equal(zoomImage.alt, thumbnail.alt);
	assert.equal(zoomCaption.textContent, thumbnail.alt);
	imageZoomDialog.listeners.click({ target: imageZoomDialog });
	assert.equal(imageZoomDialog.open, false);

	galleryView.value = 'compare';
	galleryView.listeners.change();
	assert.equal(gallery.dataset.view, 'compare');
	assert.equal(gridCaptures.hidden, true);
	assert.equal(comparisonCaptures.hidden, false);

	filter.value = 'songs';
	filter.listeners.change();
	assert.equal(card.hidden, true);
});
