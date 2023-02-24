class AttachmentType {
    static types = {
        'sheet-music': {
            title: 'Sheet Music',
            textColour: 'text-gray-500',
            icon: 'file-pdf',
        },
        'full-mix-demo': {
            title: 'Full Mix (Demo)',
            textColour: 'text-gray-500',
            icon: 'compact-disc',
        },
        'learning-tracks': {
            title: 'Learning Tracks',
            textColour: 'text-gray-500',
            icon: 'file-audio',
        },
        'youtube': {
            title: 'YouTube',
            textColour: 'text-gray-500',
            icon: 'video',
        },
        'other': {
            title: 'Other',
            textColour: 'text-gray-500',
            icon: 'file',
        }
    };

    slug = 'other';

    constructor(slug) {
        this.slug = slug;
    }

    static get(slug) {
        return new AttachmentType(slug);
    }

    get title() { return AttachmentType.types[this.slug].title; }
    get textColour() { return AttachmentType.types[this.slug].textColour; }
    get icon() { return AttachmentType.types[this.slug].icon; }

    get test() { return 'hello'; }

    get isAudio() {
        return [
            AttachmentType.types['learning-tracks'],
            AttachmentType.types['full-mix-demo'],
        ]
        .includes(AttachmentType.types[this.slug]);
    }

    get isVideo() {
        return [AttachmentType.types['youtube']].includes(AttachmentType.types[this.slug]);
    }

    get isPdf() {
        return [AttachmentType.types['sheet-music']].includes(AttachmentType.types[this.slug]);
    }

    static slugify(str) {
        return str
            .toLowerCase()
            .trim()
            .replace(/[^\w\s-]/g, '')
            .replace(/[\s_-]+/g, '-')
            .replace(/^-+|-+$/g, '');
    }
}

export default AttachmentType;