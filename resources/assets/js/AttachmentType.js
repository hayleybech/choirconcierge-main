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

    get title() { return AttachmentType.types[this.slug].title; }
    get textColour() { return AttachmentType.types[this.slug].textColour; }
    get icon() { return AttachmentType.types[this.slug].icon; }

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