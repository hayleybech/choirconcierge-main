const assert = require('node:assert/strict');
const fs = require('node:fs');
const os = require('node:os');
const path = require('node:path');
const test = require('node:test');
const {
	assertNoSymlinkComponents,
	partitionCases,
	runWorkers,
	screenshotPath,
	validateCaptureCompleteness,
	validateCatalog,
} = require('./visual-regression-runner');

test('partitions every case deterministically without overlap', () => {
	const cases = Array.from({ length: 7 }, (_, index) => ({ id: `case-${index}` }));
	const partitions = partitionCases(cases, 3);

	assert.deepEqual(partitions, [
		[cases[0], cases[3], cases[6]],
		[cases[1], cases[4]],
		[cases[2], cases[5]],
	]);
	assert.deepEqual(partitionCases(cases, 3), partitions);
	assert.deepEqual(partitions.flat().map(({ id }) => id).sort(), cases.map(({ id }) => id).sort());
	assert.throws(() => partitionCases(cases, 0), /positive integer/);
});

test('runs partitions within the configured worker limit', async () => {
	let activeWorkers = 0;
	let maximumActiveWorkers = 0;
	const completed = [];

	await runWorkers(
		[['case-one'], ['case-two'], ['case-three'], ['case-four']],
		2,
		async partition => {
			activeWorkers += 1;
			maximumActiveWorkers = Math.max(maximumActiveWorkers, activeWorkers);
			await new Promise(resolve => setTimeout(resolve, 5));
			completed.push(...partition);
			activeWorkers -= 1;
		}
	);

	assert.equal(maximumActiveWorkers, 2);
	assert.deepEqual(completed.sort(), ['case-four', 'case-one', 'case-three', 'case-two']);
});

test('waits for workers and propagates a worker failure', async () => {
	let otherWorkerFinished = false;

	await assert.rejects(
		runWorkers([['fails'], ['finishes']], 2, async ([caseId]) => {
			if (caseId === 'fails') {
				throw new Error('worker failed');
			}

			await new Promise(resolve => setTimeout(resolve, 5));
			otherWorkerFinished = true;
		}),
		/worker failed/
	);
	assert.equal(otherWorkerFinished, true);
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
