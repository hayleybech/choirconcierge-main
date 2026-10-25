const fs = require('node:fs');
const path = require('node:path');
const { spawnSync } = require('node:child_process');
const sharp = require('sharp');
const { auditCatalog } = require('./visual-regression-audit');

const root = path.resolve(__dirname, '..');
const catalogPath = path.join(root, 'resources', 'visual-regression', 'catalog.json');
const baselineDirectory = path.join(root, 'tests', 'Browser', 'Screenshots', 'Baselines');
const latestDirectory = path.join(root, 'tests', 'Browser', 'Screenshots', 'Latest');
const diffDirectory = path.join(root, 'tests', 'Browser', 'Screenshots', 'Diffs');
const safePathSegment = /^[a-z0-9]+(?:-[a-z0-9]+)*$/;

async function comparePngBuffers(expectedBuffer, capturedBuffer) {
	const [expected, captured] = await Promise.all([
		sharp(expectedBuffer)
			.ensureAlpha()
			.raw()
			.toBuffer({ resolveWithObject: true }),
		sharp(capturedBuffer)
			.ensureAlpha()
			.raw()
			.toBuffer({ resolveWithObject: true }),
	]);

	const dimensionsMatch =
		expected.info.width === captured.info.width && expected.info.height === captured.info.height;
	const width = Math.max(expected.info.width, captured.info.width);
	const height = Math.max(expected.info.height, captured.info.height);
	const diffPixels = Buffer.alloc(width * height * 4);
	let differingPixels = 0;

	for (let y = 0; y < height; y++) {
		for (let x = 0; x < width; x++) {
			const expectedOffset = (y * expected.info.width + x) * 4;
			const capturedOffset = (y * captured.info.width + x) * 4;
			const diffOffset = (y * width + x) * 4;
			const pixelExistsInExpected = x < expected.info.width && y < expected.info.height;
			const pixelExistsInCaptured = x < captured.info.width && y < captured.info.height;
			const pixelDiffers =
				!pixelExistsInExpected ||
				!pixelExistsInCaptured ||
				expected.data[expectedOffset] !== captured.data[capturedOffset] ||
				expected.data[expectedOffset + 1] !== captured.data[capturedOffset + 1] ||
				expected.data[expectedOffset + 2] !== captured.data[capturedOffset + 2] ||
				expected.data[expectedOffset + 3] !== captured.data[capturedOffset + 3];

			if (pixelDiffers) {
				differingPixels++;
				diffPixels[diffOffset] = 255;
				diffPixels[diffOffset + 3] = 255;
			}
		}
	}

	return {
		matches: differingPixels === 0,
		dimensionsMatch,
		differingPixels,
		diffImage:
			differingPixels === 0
				? null
				: await sharp(diffPixels, {
						raw: {
							width,
							height,
							channels: 4,
						},
				  })
						.png()
						.toBuffer(),
	};
}

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

async function compareCaptures(catalog, updateBaselines, compareWithBaselines) {
	const results = [];

	for (const screenshotCase of catalog.cases) {
		for (const viewport of catalog.viewports) {
			const latestPath = screenshotPath(latestDirectory, screenshotCase.id, viewport.id);
			const baselinePath = screenshotPath(baselineDirectory, screenshotCase.id, viewport.id);
			const diffPath = screenshotPath(diffDirectory, screenshotCase.id, viewport.id);
			const result = {
				case_id: screenshotCase.id,
				module: screenshotCase.module,
				page_type: screenshotCase.page_type,
				state: screenshotCase.state,
				route: screenshotCase.route,
				persona: screenshotCase.persona,
				browser: catalog.browser,
				viewport: viewport.id,
				status: 'failed',
				differing_pixels: null,
			};

			if (!fs.existsSync(latestPath)) {
				console.error(`Missing capture: ${path.relative(root, latestPath)}`);
				results.push(result);
				continue;
			}

			if (updateBaselines) {
				fs.mkdirSync(path.dirname(baselinePath), { recursive: true });
				fs.copyFileSync(latestPath, screenshotPath(baselineDirectory, screenshotCase.id, viewport.id));
				result.status = 'baseline_updated';
				console.log(`Updated baseline: ${path.relative(root, baselinePath)}`);
				results.push(result);
				continue;
			}

			if (!compareWithBaselines) {
				result.status = 'captured';
				console.log(`Captured: ${screenshotCase.id} (${viewport.id}); baseline comparison is reserved for CI.`);
				results.push(result);
				continue;
			}

			if (!fs.existsSync(baselinePath)) {
				console.error(`Missing reviewed baseline: ${path.relative(root, baselinePath)}`);
				results.push(result);
				continue;
			}

			const comparison = await comparePngBuffers(fs.readFileSync(baselinePath), fs.readFileSync(latestPath));

			result.differing_pixels = comparison.differingPixels;

			if (comparison.matches) {
				result.status = 'passed';
				console.log(`Match: ${screenshotCase.id} (${viewport.id})`);
			} else {
				result.status = 'diff';
				fs.mkdirSync(path.dirname(diffPath), { recursive: true });

				if (comparison.diffImage) {
					fs.writeFileSync(diffPath, comparison.diffImage);
					result.diff_path = path.relative(root, diffPath).replaceAll(path.sep, '/');
				}

				const difference =
					`${comparison.differingPixels} pixel(s) differ` +
					(comparison.dimensionsMatch ? '' : ' and image dimensions differ');
				console.error(`Visual regression: ${screenshotCase.id} (${viewport.id}); ${difference}.`);
			}

			results.push(result);
		}
	}

	const summaryPath = path.join(latestDirectory, 'summary.json');
	fs.writeFileSync(
		summaryPath,
		`${JSON.stringify(
			{
				schema_version: 1,
				comparison_mode: compareWithBaselines ? 'canonical-ci' : 'capture-only',
				results,
			},
			null,
			2
		)}\n`
	);

	return results;
}

async function main(args = process.argv.slice(2)) {
	const updateBaselines = args.includes('--update-baselines');
	const unknownArguments = args.filter(argument => argument !== '--update-baselines');
	const isCiEnvironment = ['true', '1'].includes((process.env.VISUAL_REGRESSION_CI ?? '').toLowerCase());

	if (unknownArguments.length > 0) {
		console.error(`Unknown argument(s): ${unknownArguments.join(', ')}`);
		return 2;
	}

	if (updateBaselines && (!isCiEnvironment || process.env.VISUAL_REGRESSION_CANONICAL !== 'true')) {
		console.error(
			'Baseline updates require VISUAL_REGRESSION_CI=true and VISUAL_REGRESSION_CANONICAL=true in the canonical renderer.'
		);
		return 2;
	}

	const catalog = readCatalog();
	const coverageAudit = auditCatalog(catalog);

	if (!coverageAudit.complete) {
		if (isCiEnvironment) {
			console.error(
				'The screenshot catalog must include every statically rendered Inertia page before CI capture can pass.'
			);
			return 1;
		}

		console.warn(
			`Catalog coverage is incomplete: ${coverageAudit.missingComponents.length} page component(s) are missing, ` +
				`${coverageAudit.unrecognizedComponents.length} catalog component(s) are unrecognized, ` +
				`and ${coverageAudit.dynamicRenderCalls} render call(s) are dynamic.`
		);
	}

	for (const outputDirectory of [baselineDirectory, latestDirectory, diffDirectory]) {
		assertNoSymlinkComponents(outputDirectory);
	}

	fs.rmSync(latestDirectory, { recursive: true, force: true });
	fs.rmSync(diffDirectory, { recursive: true, force: true });
	fs.mkdirSync(latestDirectory, { recursive: true });

	if (runCommand('npm', ['run', 'prod']) !== 0) {
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

	const results = await compareCaptures(catalog, updateBaselines, isCiEnvironment);

	return results.every(result => ['passed', 'baseline_updated', 'captured'].includes(result.status)) ? 0 : 1;
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

module.exports = { assertNoSymlinkComponents, comparePngBuffers, main, screenshotPath, validateCatalog };
