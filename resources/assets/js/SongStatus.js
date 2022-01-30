class SongStatus {
    static statuses = {
        pending: {
            title: 'Pending',
            textColour: 'text-red-500',
            icon: 'lock',
        },
        learning: {
            title: 'Learning',
            textColour: 'text-amber-500',
            icon: 'circle',
        },
        active: {
            title: 'Active',
            textColour: 'text-emerald-500',
            icon: 'circle',
        },
        archived: {
            title: 'Archived',
            textColour: 'text-blue-500',
            icon: 'circle',
        }
    };

    slug = 'pending';

    constructor(slug) {
        this.slug = slug;
    }

    get title() { return SongStatus.statuses[this.slug].title; }
    get textColour() { return SongStatus.statuses[this.slug].textColour; }
    get icon() { return SongStatus.statuses[this.slug].icon; }
}

export default SongStatus;