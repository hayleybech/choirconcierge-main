const fs = require('node:fs');
const path = require('node:path');
const { spawnSync } = require('node:child_process');
const { auditCatalog } = require('./visual-regression-audit');

const root = path.resolve(__dirname, '..');
const catalogPath = path.join(root, 'resources', 'visual-regression', 'catalog.json');
const latestDirectory = path.join(root, 'tests', 'Browser', 'Screenshots', 'ArgosCaptures');
const safePathSegment = /^[a-z0-9]+(?:-[a-z0-9]+)*$/;

function readCatalog() {
	return validateCatalog(JSON.parse(fs.readFileSync(catalogPath, 'utf8')));
}

function validateCatalog(catalog) {
	if (catalog.schema_version !== 1 || !Array.isArray(catalog.cases) || !Array.isArray(catalog.viewports)) {
		throw new Error('The visual-regression catalog has an unsupported or invalid schema.');
	}

	for (const [records, label] of [
		[catalog.cases, 'case'],
		[catalog.viewports, 'viewport'],
	]) {
		const ids = new Set();

		for (const record of records) {
			if (!record || typeof record.id !== 'string' || !safePathSegment.test(record.id)) {
				throw new Error(`Visual-regression ${label} IDs must be safe lowercase path segments.`);
			}

			if (ids.has(record.id)) {
				throw new Error(`Visual-regression ${label} IDs must be unique.`);
			}

			ids.add(record.id);
		}
	}

	return catalog;
}

function runCommand(command, args, options = {}) {
	const result = spawnSync(command, args, {
		cwd: root,
		stdio: 'inherit',
		shell: process.platform === 'win32' && ['npm', 'php'].includes(command),
		...options,
	});

	if (result.error) {
		throw result.error;
	}

	return result.status ?? 1;
}

function screenshotPath(directory, caseId, viewportId) {
	if (!safePathSegment.test(caseId) || !safePathSegment.test(viewportId)) {
		throw new Error('Screenshot path IDs must be safe lowercase path segments.');
	}

	const directoryPath = path.resolve(directory);
	const filePath = path.resolve(directoryPath, caseId, `${viewportId}.png`);
	const relativePath = path.relative(directoryPath, filePath);

	if (relativePath === '..' || relativePath.startsWith(`..${path.sep}`) || path.isAbsolute(relativePath)) {
		throw new Error('Screenshot output path must remain inside its configured directory.');
	}

	assertNoSymlinkComponents(filePath);

	return filePath;
}

function validateCaptureCompleteness(catalog, directory) {
	const expectedPaths = catalog.cases.flatMap(screenshotCase =>
		catalog.viewports.map(viewport => screenshotPath(directory, screenshotCase.id, viewport.id))
	);
	const missingPaths = expectedPaths.filter(filePath => !fs.existsSync(filePath));
	const pngCount = fs.existsSync(directory)
		? findPngFiles(directory).length
		: 0;

	if (missingPaths.length > 0 || pngCount !== expectedPaths.length) {
		throw new Error(
			`Incomplete Argos capture: expected ${expectedPaths.length} PNGs, found ${pngCount}, ` +
				`${missingPaths.length} expected capture(s) missing.`
		);
	}

	return expectedPaths.length;
}

function findPngFiles(directory) {
	return fs.readdirSync(directory, { withFileTypes: true }).flatMap(entry => {
		const entryPath = path.join(directory, entry.name);

		if (entry.isDirectory()) {
			return findPngFiles(entryPath);
		}

		return entry.isFile() && entry.name.toLowerCase().endsWith('.png') ? [entryPath] : [];
	});
}

function assertNoSymlinkComponents(targetPath) {
	const absolutePath = path.resolve(targetPath);
	const filesystemRoot = path.parse(absolutePath).root;
	let currentPath = filesystemRoot;

	for (const component of path
		.relative(filesystemRoot, absolutePath)
		.split(path.sep)
		.filter(Boolean)) {
		currentPath = path.join(currentPath, component);

		try {
			if (fs.lstatSync(currentPath).isSymbolicLink()) {
				throw new Error(`Screenshot output path must not contain symbolic links: ${currentPath}`);
			}
		} catch (error) {
			if (error.code !== 'ENOENT') {
				throw error;
			}
		}
	}
}

async function main() {
	const catalog = readCatalog();
	const coverageAudit = auditCatalog(catalog);

	if (!coverageAudit.complete) {
		console.error('The screenshot catalog must include every statically rendered Inertia page before capture can pass.');
		return 1;
	}

	assertNoSymlinkComponents(latestDirectory);

	fs.rmSync(latestDirectory, { recursive: true, force: true });
	fs.mkdirSync(latestDirectory, { recursive: true });

	if (runCommand('npm', ['run', 'build']) !== 0) {
		return 1;
	}

	if (
		runCommand('php', ['artisan', 'test', '--compact', 'tests/Browser/VisualRegressionCaptureTest.php'], {
			env: {
				...process.env,
				APP_ENV: 'testing',
				SESSION_DRIVER: 'file',
				TENANCY_CENTRAL_DOMAIN: '127.0.0.1',
			},
		}) !== 0
	) {
		return 1;
	}

	const captureCount = validateCaptureCompleteness(catalog, latestDirectory);
	console.log(`Captured ${captureCount} catalog screenshots for Argos.`);
	return 0;
}

if (require.main === module) {
	main()
		.then(exitCode => {
			process.exitCode = exitCode;
		})
		.catch(error => {
			console.error(error);
			process.exitCode = 1;
		});
}

module.exports = {
	assertNoSymlinkComponents,
	main,
	screenshotPath,
	validateCaptureCompleteness,
	validateCatalog,
};
