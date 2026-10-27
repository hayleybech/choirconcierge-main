const assert = require('node:assert/strict');
const fs = require('node:fs');
const os = require('node:os');
const path = require('node:path');
const test = require('node:test');
const {
	assertNoSymlinkComponents,
	screenshotPath,
	validateCaptureCompleteness,
	validateCatalog,
} = require('./visual-regression-runner');

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
		() => screenshotPath('tests/Browser/Screenshots/ArgosCaptures', '..', 'desktop'),
		/safe lowercase path segments/
	);
});

test('requires exactly one capture for every catalog case and viewport', () => {
	const directory = fs.mkdtempSync(path.join(os.tmpdir(), 'argos-captures-'));
	const catalog = {
		cases: [{ id: 'case-one' }, { id: 'case-two' }],
		viewports: [{ id: 'desktop' }, { id: 'mobile' }],
	};

	try {
		for (const screenshotCase of catalog.cases) {
			for (const viewport of catalog.viewports) {
				const filePath = screenshotPath(directory, screenshotCase.id, viewport.id);
				fs.mkdirSync(path.dirname(filePath), { recursive: true });
				fs.writeFileSync(filePath, 'png');
			}
		}

		assert.equal(validateCaptureCompleteness(catalog, directory), 4);
		const unexpectedPath = path.join(directory, 'unexpected.png');
		fs.writeFileSync(unexpectedPath, 'png');
		assert.throws(() => validateCaptureCompleteness(catalog, directory), /Incomplete Argos capture/);
		fs.unlinkSync(unexpectedPath);
		fs.unlinkSync(screenshotPath(directory, 'case-two', 'mobile'));
		assert.throws(() => validateCaptureCompleteness(catalog, directory), /Incomplete Argos capture/);
	} finally {
		fs.rmSync(directory, { recursive: true, force: true });
	}
});

test('rejects symlinked screenshot directories', () => {
	const captureDirectory = path.resolve('tests/Browser/Screenshots/ArgosCaptures');
	const symlinkedCaseDirectory = path.join(captureDirectory, 'auth-login');
	const originalLstat = fs.lstatSync;

	fs.lstatSync = (filePath, ...args) =>
		path.resolve(filePath) === symlinkedCaseDirectory
			? { isSymbolicLink: () => true }
			: originalLstat(filePath, ...args);

	try {
		assert.throws(
			() => screenshotPath(captureDirectory, 'auth-login', 'desktop'),
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
			() => assertNoSymlinkComponents(path.join(screenshotsDirectory, 'ArgosCaptures')),
			/must not contain symbolic links/
		);
	} finally {
		fs.lstatSync = originalLstat;
	}
});
