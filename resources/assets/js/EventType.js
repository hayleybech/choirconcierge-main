class EventType {
    static types = {
        'Performance': {
            badgeColour: 'bg-orange-400/50',
            dotColour: 'bg-orange-400',
        },
        'Rehearsal': {
            badgeColour: 'bg-lime-400/50',
            dotColour: 'bg-lime-400',
        },
        'Social Event': {
            badgeColour: 'bg-sky-400/50',
            dotColour: 'bg-sky-400'
        },
        'Other': {
            badgeColour: 'bg-gray-400/50',
            dotColour: 'bg-gray-400',
        },
    };

    title = 'Other';

    constructor(title) {
        this.title = title;
    }

    get badgeColour() { return EventType.types[this.title].badgeColour; }
    get dotColour() { return EventType.types[this.title].dotColour; }
}

export default EventType;