const fs = require('node:fs');
const path = require('node:path');

const root = path.resolve(__dirname, '..');
const catalogPath = path.join(root, 'resources', 'visual-regression', 'catalog.json');

function extractInertiaComponents(source) {
	return [...source.matchAll(/\bInertia\s*::\s*render\s*\(\s*(['"])([^'"]+)\1/g)].map(match => match[2]);
}

function findPhpFiles(directory) {
	if (!fs.existsSync(directory)) {
		return [];
	}

	return fs.readdirSync(directory, { withFileTypes: true }).flatMap(entry => {
		const entryPath = path.join(directory, entry.name);

		if (entry.isDirectory()) {
			return findPhpFiles(entryPath);
		}

		return entry.isFile() && entry.name.endsWith('.php') ? [entryPath] : [];
	});
}

function auditCatalog(catalog, projectRoot = root) {
	const sourcePaths = findPhpFiles(path.join(projectRoot, 'app'));
	const renderedComponents = new Set();
	let dynamicRenderCalls = 0;

	for (const sourcePath of sourcePaths) {
		if (!fs.existsSync(sourcePath)) {
			continue;
		}

		const source = fs.readFileSync(sourcePath, 'utf8');
		const componentMatches = extractInertiaComponents(source);
		const renderCalls = source.match(/\bInertia\s*::\s*render\s*\(/g) ?? [];

		componentMatches.forEach(component => renderedComponents.add(component));
		dynamicRenderCalls += renderCalls.length - componentMatches.length;
	}

	const cataloguedComponents = new Set(catalog.cases.map(screenshotCase => screenshotCase.component));
	const missingComponents = [...renderedComponents].filter(component => !cataloguedComponents.has(component)).sort();
	const unrecognizedComponents = [...cataloguedComponents]
		.filter(component => !renderedComponents.has(component))
		.sort();

	return {
		renderedComponents: [...renderedComponents].sort(),
		missingComponents,
		unrecognizedComponents,
		dynamicRenderCalls,
		complete: missingComponents.length === 0 && unrecognizedComponents.length === 0 && dynamicRenderCalls === 0,
	};
}

function main() {
	const catalog = JSON.parse(fs.readFileSync(catalogPath, 'utf8'));
	const audit = auditCatalog(catalog);

	console.log(
		`Catalog coverage: ${catalog.cases.length} screenshot cases; ${audit.renderedComponents.length} statically rendered page components.`
	);

	if (audit.missingComponents.length > 0) {
		console.error('Page components missing from the screenshot catalog:');
		audit.missingComponents.forEach(component => console.error(`- ${component}`));
	}

	if (audit.unrecognizedComponents.length > 0) {
		console.error('Catalog components not found in a controller or exception renderer:');
		audit.unrecognizedComponents.forEach(component => console.error(`- ${component}`));
	}

	if (audit.dynamicRenderCalls > 0) {
		console.error(`Found ${audit.dynamicRenderCalls} dynamic Inertia render call(s) that need explicit review.`);
	}

	return audit.complete ? 0 : 1;
}

if (require.main === module) {
	process.exitCode = main();
}

module.exports = { auditCatalog, extractInertiaComponents };
