const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const test = require('node:test');
const sharp = require('sharp');
const {
	assertNoSymlinkComponents,
	comparePngBuffers,
	screenshotPath,
	validateCatalog,
} = require('./visual-regression-runner');

async function createPng(width, height, background) {
	return sharp({
		create: {
			width,
			height,
			channels: 4,
			background,
		},
	})
		.png()
		.toBuffer();
}

test('strictly accepts identical screenshot pixels', async () => {
	const screenshot = await createPng(2, 1, '#ffffff');
	const comparison = await comparePngBuffers(screenshot, screenshot);

	assert.equal(comparison.matches, true);
	assert.equal(comparison.differingPixels, 0);
});

test('strictly rejects even one changed screenshot pixel', async () => {
	const expected = await createPng(2, 1, '#ffffff');
	const captured = await sharp({
		create: {
			width: 2,
			height: 1,
			channels: 4,
			background: '#ffffff',
		},
	})
		.composite([{ input: await createPng(1, 1, '#ff0000'), left: 0, top: 0 }])
		.png()
		.toBuffer();
	const comparison = await comparePngBuffers(expected, captured);

	assert.equal(comparison.matches, false);
	assert.equal(comparison.differingPixels, 1);
	assert.ok(Buffer.isBuffer(comparison.diffImage));
});

test('treats screenshot dimension changes as mismatches', async () => {
	const expected = await createPng(1, 1, '#ffffff');
	const captured = await createPng(2, 1, '#ffffff');
	const comparison = await comparePngBuffers(expected, captured);

	assert.equal(comparison.matches, false);
	assert.equal(comparison.dimensionsMatch, false);
	assert.equal(comparison.differingPixels, 1);
});

test('rejects unsafe catalog IDs before constructing screenshot paths', () => {
	assert.throws(
		() =>
			validateCatalog({
				schema_version: 1,
				cases: [{ id: '../outside' }],
				viewports: [{ id: 'desktop' }],
			}),
		/safe lowercase path segments/
	);

	assert.throws(
		() => screenshotPath('tests/Browser/Screenshots/Baselines', '..', 'desktop'),
		/safe lowercase path segments/
	);
});

test('rejects symlinked screenshot directories', () => {
	const baselineDirectory = path.resolve('tests/Browser/Screenshots/Baselines');
	const symlinkedCaseDirectory = path.join(baselineDirectory, 'auth-login');
	const originalLstat = fs.lstatSync;

	fs.lstatSync = (filePath, ...args) =>
		path.resolve(filePath) === symlinkedCaseDirectory
			? { isSymbolicLink: () => true }
			: originalLstat(filePath, ...args);

	try {
		assert.throws(
			() => screenshotPath(baselineDirectory, 'auth-login', 'desktop'),
			/must not contain symbolic links/
		);
	} finally {
		fs.lstatSync = originalLstat;
	}
});

test('rejects symlinked output roots before cleanup can begin', () => {
	const screenshotsDirectory = path.resolve('tests/Browser/Screenshots');
	const originalLstat = fs.lstatSync;

	fs.lstatSync = (filePath, ...args) =>
		path.resolve(filePath) === screenshotsDirectory
			? { isSymbolicLink: () => true }
			: originalLstat(filePath, ...args);

	try {
		assert.throws(
			() => assertNoSymlinkComponents(path.join(screenshotsDirectory, 'Latest')),
			/must not contain symbolic links/
		);
	} finally {
		fs.lstatSync = originalLstat;
	}
});
