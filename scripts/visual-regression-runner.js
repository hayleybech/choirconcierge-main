const fs = require('node:fs');
const path = require('node:path');
const { spawn, spawnSync } = require('node:child_process');
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

function partitionCases(cases, concurrency) {
	if (!Number.isInteger(concurrency) || concurrency < 1) {
		throw new Error('Visual-regression concurrency must be a positive integer.');
	}

	const partitions = Array.from({ length: Math.min(concurrency, cases.length) }, () => []);

	for (const [index, screenshotCase] of cases.entries()) {
		partitions[index % partitions.length].push(screenshotCase);
	}

	return partitions;
}

async function runWorkers(partitions, concurrency, runWorker) {
	if (!Number.isInteger(concurrency) || concurrency < 1) {
		throw new Error('Visual-regression concurrency must be a positive integer.');
	}

	let nextPartition = 0;
	let workerError;

	await Promise.all(
		Array.from({ length: Math.min(concurrency, partitions.length) }, async () => {
			while (nextPartition < partitions.length && !workerError) {
				const partition = partitions[nextPartition++];

				try {
					await runWorker(partition);
				} catch (error) {
					workerError ??= error;
				}
			}
		})
	);

	if (workerError) {
		throw workerError;
	}
}

function runCaptureWorker(screenshotCases) {
	return new Promise((resolve, reject) => {
		const child = spawn(
			'php',
			[
				'artisan',
				'test',
				'--compact',
				'--filter=captures',
				'tests/Browser/VisualRegressionCaptureTest.php',
			],
			{
				cwd: root,
				stdio: 'inherit',
				shell: process.platform === 'win32',
				env: {
					...process.env,
					APP_ENV: 'testing',
					SESSION_DRIVER: 'file',
					TENANCY_CENTRAL_DOMAIN: '127.0.0.1',
					VISUAL_REGRESSION_DATABASE_PREPARED: '1',
					VISUAL_REGRESSION_CASE_IDS: JSON.stringify(screenshotCases.map(({ id }) => id)),
					VISUAL_REGRESSION_WORKER_ID: screenshotCases[0].id,
				},
			}
		);

		child.once('error', reject);
		child.once('exit', (code, signal) => {
			if (code === 0) {
				resolve();
				return;
			}

			reject(new Error(`Visual-regression worker failed${signal ? ` with signal ${signal}` : ` with exit code ${code}`}.`));
		});
	});
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
	const concurrency = Number(process.env.VISUAL_REGRESSION_CONCURRENCY ?? '2');

	if (!Number.isInteger(concurrency) || concurrency < 1) {
		throw new Error('VISUAL_REGRESSION_CONCURRENCY must be a positive integer.');
	}

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

	const testEnvironment = {
		...process.env,
		APP_ENV: 'testing',
		SESSION_DRIVER: 'file',
		TENANCY_CENTRAL_DOMAIN: '127.0.0.1',
	};

	if (runCommand('php', ['artisan', 'migrate:fresh', '--env=testing'], { env: testEnvironment }) !== 0) {
		return 1;
	}

	const partitions = partitionCases(catalog.cases, concurrency);
	await runWorkers(partitions, concurrency, runCaptureWorker);

	if (
		runCommand(
			'php',
			[
				'artisan',
				'test',
				'--compact',
				'--filter=stabilization',
				'tests/Browser/VisualRegressionCaptureTest.php',
			],
			{
				env: {
					...testEnvironment,
					VISUAL_REGRESSION_DATABASE_PREPARED: '1',
				},
			}
		) !== 0
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
	partitionCases,
	runWorkers,
	screenshotPath,
	validateCaptureCompleteness,
	validateCatalog,
};
