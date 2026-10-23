const assert = require('node:assert/strict');
const test = require('node:test');
const { auditCatalog, extractInertiaComponents } = require('./visual-regression-audit');

test('extracts static Inertia page component names', () => {
	const components = extractInertiaComponents(`
        Inertia :: render (
            'Songs/Index'
        );
        Inertia::render("Events/Show", $props);
        Inertia::render($component);
    `);

	assert.deepEqual(components, ['Songs/Index', 'Events/Show']);
});

test('reports catalog components with no matching rendered page', () => {
	const audit = auditCatalog(
		{
			cases: [{ component: 'Songs/Index' }],
		},
		'unused-project-root'
	);

	assert.equal(audit.complete, false);
	assert.deepEqual(audit.renderedComponents, []);
	assert.deepEqual(audit.unrecognizedComponents, ['Songs/Index']);
});
