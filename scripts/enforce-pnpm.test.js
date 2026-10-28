const assert = require('node:assert/strict');
const { spawnSync } = require('node:child_process');
const path = require('node:path');
const test = require('node:test');
const packageJson = require('../package.json');

const guardPath = path.join(__dirname, 'enforce-pnpm.js');

function runGuard(userAgent) {
    return spawnSync(process.execPath, [guardPath], {
        encoding: 'utf8',
        env: {
            ...process.env,
            npm_config_user_agent: userAgent,
        },
    });
}

test('runs the package manager guard during dependency installation', () => {
    assert.equal(packageJson.scripts.preinstall, 'node scripts/enforce-pnpm.js');
});

test('allows pnpm installations', () => {
    const result = runGuard('pnpm/12.15.0 npm/? node/v22.23.3 win32 x64');

    assert.equal(result.status, 0, result.stderr);
});

for (const [packageManager, userAgent] of [
    ['npm', 'npm/10.9.9 node/v22.23.3 win32 x64'],
    ['Yarn', 'yarn/1.22.22 npm/? node/v22.23.3 win32 x64'],
]) {
    test(`rejects ${packageManager} installations with pnpm guidance`, () => {
        const result = runGuard(userAgent);

        assert.equal(result.status, 1, result.stderr);
        assert.match(result.stderr, /This project uses pnpm as its only supported JavaScript package manager/);
        assert.match(result.stderr, /corepack pnpm install/);
    });
}