class SingerStatus {
    static statuses = {
        members: {
            title: 'Members',
            textColour: 'text-emerald-500',
            icon: 'circle',
        },
        prospects: {
            title: 'Prospects',
            textColour: 'text-amber-500',
            icon: 'circle',
        },
        'archived-prospects': {
            title: 'Archived Prospects',
            textColour: 'text-amber-700',
            icon: 'circle',
        },
        'archived-members': {
            title: 'Archived Members',
            textColour: 'text-emerald-700',
            icon: 'circle',
        }
    };

    slug = 'pending';

    constructor(slug) {
        this.slug = slug;
    }

    get title() { return SingerStatus.statuses[this.slug].title; }
    get textColour() { return SingerStatus.statuses[this.slug].textColour; }
    get icon() { return SingerStatus.statuses[this.slug].icon; }
}

export default SingerStatus;