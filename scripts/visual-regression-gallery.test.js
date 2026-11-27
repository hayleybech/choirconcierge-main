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
		options: [],
		value: '',
		open: false,
		src: '',
		alt: '',
		addEventListener(event, listener) {
			this.listeners[event] = listener;
		},
		dispatchEvent(event) {
			this.listeners[event.type]();
		},
		querySelector(selector) {
			return this[selector];
		},
		closest(selector) {
			return this.ancestors?.[selector] ?? null;
		},
		showModal() {
			this.open = true;
		},
		close() {
			this.open = false;
		},
	};
}

test('filters catalog captures and navigates between zoomed images', () => {
	const imageZoomDialog = createElement();
	const zoomImage = createElement();
	const zoomCaption = createElement();
	const zoomHelp = createElement({}, true);
	imageZoomDialog['[data-zoom-image]'] = zoomImage;
	imageZoomDialog['[data-zoom-caption]'] = zoomCaption;
	imageZoomDialog['[data-zoom-help]'] = zoomHelp;
	const cards = [
		createElement({ module: 'auth', state: 'normal', viewport: 'Desktop' }),
		createElement({ module: 'songs', state: 'normal', viewport: 'Desktop' }),
		createElement({ module: 'songs', state: 'empty', viewport: 'Desktop' }),
	];
	const thumbnail = createElement();
	thumbnail.src = '/screenshots/auth-login/desktop/latest';
	thumbnail.alt = 'Latest capture for Auth/Login at Desktop';
	const zoomTrigger = createElement();
	zoomTrigger['img'] = thumbnail;
	zoomTrigger.ancestors = { '[data-screenshot]': cards[0] };
	const nextThumbnail = createElement();
	nextThumbnail.src = '/screenshots/tenant-songs-index/desktop/latest';
	nextThumbnail.alt = 'Latest capture for Songs/Index at Desktop';
	const nextZoomTrigger = createElement();
	nextZoomTrigger['img'] = nextThumbnail;
	nextZoomTrigger.ancestors = { '[data-screenshot]': cards[1] };
	const moduleFilter = createElement({ filter: 'module' });
	moduleFilter.options = [{ value: '' }, { value: 'auth' }, { value: 'songs' }];
	const stateFilter = createElement({ filter: 'state' });
	stateFilter.value = 'normal';
	const viewportFilter = createElement({ filter: 'viewport' });
	viewportFilter.options = [{ value: '' }, { value: 'Desktop' }, { value: 'Mobile' }];
	viewportFilter.value = 'Desktop';
	const screenshotCount = createElement();
	const selectors = {
		'[data-image-zoom]': imageZoomDialog,
		'[data-screenshot-count]': screenshotCount,
	};
	const document = {
		querySelector(selector) {
			return selectors[selector];
		},
		querySelectorAll(selector) {
			return {
				'[data-filter]': [moduleFilter, stateFilter, viewportFilter],
				'[data-screenshot]': cards,
				'[data-zoom-trigger]': [zoomTrigger, nextZoomTrigger],
			}[selector];
		},
	};
	const viewPath = path.join(__dirname, '..', 'resources', 'views', 'visual-regression', 'gallery.blade.php');
	const view = fs.readFileSync(viewPath, 'utf8');
	const script = view.match(/<script>([\s\S]*?)<\/script>/);

	assert.ok(script, 'the gallery should contain its client-side controls');
	vm.runInNewContext(script[1], { document, Event: class Event { constructor(type) { this.type = type; } } });
	assert.equal(screenshotCount.textContent, 'Showing 2 of 3 screenshots');

	zoomTrigger.listeners.click();
	assert.equal(imageZoomDialog.open, true);
	assert.equal(zoomHelp.hidden, false);
	assert.equal(zoomImage.src, thumbnail.src);
	assert.equal(zoomImage.alt, thumbnail.alt);
	assert.equal(zoomCaption.textContent, thumbnail.alt);
	let deviceKeyPrevented = false;
	imageZoomDialog.listeners.keydown({ key: 'r', preventDefault() { deviceKeyPrevented = true; } });
	assert.equal(deviceKeyPrevented, true);
	assert.equal(viewportFilter.value, 'Mobile');
	assert.match(zoomCaption.textContent, /no matching image for the selected device/);
	viewportFilter.value = 'Desktop';
	viewportFilter.listeners.change();
	assert.equal(zoomCaption.textContent, thumbnail.alt);
	let moduleKeyPrevented = false;
	imageZoomDialog.listeners.keydown({ key: 'm', preventDefault() { moduleKeyPrevented = true; } });
	assert.equal(moduleKeyPrevented, true);
	assert.equal(moduleFilter.value, 'auth');
	moduleFilter.value = '';
	moduleFilter.listeners.change();

	let rightArrowPrevented = false;
	imageZoomDialog.listeners.keydown({ key: 'ArrowRight', preventDefault() { rightArrowPrevented = true; } });
	assert.equal(rightArrowPrevented, true);
	assert.equal(zoomImage.src, nextThumbnail.src);
	assert.equal(zoomImage.alt, nextThumbnail.alt);
	assert.equal(zoomCaption.textContent, nextThumbnail.alt);

	imageZoomDialog.listeners.keydown({ key: 'ArrowLeft', preventDefault() {} });
	assert.equal(zoomImage.src, thumbnail.src);
	assert.equal(zoomCaption.textContent, thumbnail.alt);

	imageZoomDialog.listeners.click({ target: imageZoomDialog });
	assert.equal(imageZoomDialog.open, false);

	moduleFilter.value = 'songs';
	moduleFilter.listeners.change();
	assert.equal(cards[0].hidden, true);
	assert.equal(cards[1].hidden, false);
	assert.equal(screenshotCount.textContent, 'Showing 1 of 3 screenshots');

	stateFilter.value = '';
	stateFilter.listeners.change();
	assert.equal(cards[2].hidden, false);
	assert.equal(screenshotCount.textContent, 'Showing 2 of 3 screenshots');
});
