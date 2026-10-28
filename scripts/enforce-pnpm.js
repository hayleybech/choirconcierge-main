const packageManagerUserAgent = process.env.npm_config_user_agent ?? '';

if (!packageManagerUserAgent.startsWith('pnpm/')) {
    console.error('This project uses pnpm as its only supported JavaScript package manager.');
    console.error('Use `corepack pnpm install` instead.');
    process.exit(1);
}