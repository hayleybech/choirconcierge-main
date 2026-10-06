const fs = require('node:fs');
const path = require('node:path');
const sharp = require('sharp');

const projectRoot = path.resolve(__dirname, '..');
const defaultStyle = 'solid';

function usage() {
    console.error('Usage: npm run icon -- --icon=heart --color=#ff0000 --size=32 [--style=solid]');
    process.exit(1);
}

function argumentsFromProcess() {
    return process.argv.slice(2).reduce((argumentsObject, argument) => {
        const [name, ...value] = argument.replace(/^--/, '').split('=');

        if (name && value.length > 0) {
            argumentsObject[name] = value.join('=');
        }

        return argumentsObject;
    }, {});
}

function validate(argumentsObject) {
    if (!argumentsObject.icon || !argumentsObject.color || !argumentsObject.size) {
        usage();
    }

    if (!/^#?(?:[\da-f]{3}|[\da-f]{6})$/i.test(argumentsObject.color)) {
        throw new Error('Colour must be a 3 or 6 digit hexadecimal value.');
    }

    if (!/^\d+$/.test(argumentsObject.size) || Number(argumentsObject.size) < 1) {
        throw new Error('Size must be a positive integer.');
    }
}

function findIcon(style, iconName) {
    const iconFile = path.join(projectRoot, 'public', 'vendor', 'fontawesome-pro', 'js', `${style}.js`);

    if (!fs.existsSync(iconFile)) {
        throw new Error(`Unknown Font Awesome style: ${style}`);
    }

    const source = fs.readFileSync(iconFile, 'utf8');
    const escapedName = iconName.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    const match = source.match(new RegExp(`"${escapedName}": \\[([0-9]+), ([0-9]+), \\[\\], "[^"]+", "([^"]*)"\\]`));

    if (!match) {
        throw new Error(`Icon not found: ${style}/${iconName}`);
    }

    return { width: Number(match[1]), height: Number(match[2]), path: match[3] };
}

async function renderIcon() {
    const argumentsObject = argumentsFromProcess();
    validate(argumentsObject);

    const style = argumentsObject.style || defaultStyle;
    const icon = findIcon(style, argumentsObject.icon);
    const colour = argumentsObject.color.startsWith('#') ? argumentsObject.color : `#${argumentsObject.color}`;
    const outputDirectory = path.join(projectRoot, 'public', 'img', 'email');
    const output = path.join(outputDirectory, `${argumentsObject.icon}.png`);
    const size = Number(argumentsObject.size);
    const svg = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ${icon.width} ${icon.height}"><path fill="${colour}" d="${icon.path}"/></svg>`;

    fs.mkdirSync(outputDirectory, { recursive: true });

    await sharp(Buffer.from(svg))
        .trim()
        .resize({ width: size, height: size, fit: 'inside' })
        .png()
        .toFile(output);

    console.log(`Created ${output}`);
}

renderIcon().catch((error) => {
    console.error(error.message);
    process.exit(1);
});