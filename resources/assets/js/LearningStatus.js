class LearningStatus {
    static statuses = {
        'not-started': {
            title: 'Learning',
            shortTitle: 'Learning',
            textColour: 'text-red-500',
            iconTextColour: '!text-red-500',
            icon: 'clock',
        },
        'assessment-ready': {
            title: 'Assessment Ready',
            shortTitle: 'Assessing',
            textColour: 'text-amber-500',
            iconTextColour: '!text-amber-500',
            icon: 'check',
        },
        'performance-ready': {
            title: 'Performance Ready',
            shortTitle: 'Performing',
            textColour: 'text-emerald-500',
            iconTextColour: '!text-emerald-500',
            icon: 'check-double',
        },
    };

    slug = 'not-started';

    constructor(slug) {
        this.slug = slug;
    }

    get title() { return LearningStatus.statuses[this.slug].title; }
    get shortTitle() { return LearningStatus.statuses[this.slug].shortTitle; }
    get textColour() { return LearningStatus.statuses[this.slug].textColour; }
    get iconTextColour() { return LearningStatus.statuses[this.slug].iconTextColour; }
    get icon() { return LearningStatus.statuses[this.slug].icon; }
}

export default LearningStatus;