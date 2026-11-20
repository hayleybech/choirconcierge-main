import React from 'react';
import Badge from './Badge';
import EventType from '../EventType';

const EventTypeBadge = ({ eventTypeName, compact = false }) => (
	<Badge colour={new EventType(eventTypeName).badgeColour} compact={compact}>
		{eventTypeName}
	</Badge>
);

export default EventTypeBadge;