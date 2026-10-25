<?php

namespace App\Enums;

enum ProductAnnouncementAudience: string
{
    case ALL_USERS = 'all-users';
    case ALL_ACTIVE_MEMBERS = 'all-active-members';
    case CHOIR_ADMINS = 'choir-admins';
    case MUSIC_TEAM = 'music-team';
    case EVENTS_TEAM = 'events-team';
    case MEMBERSHIP_TEAM = 'membership-team';

    public function label(): string
    {
        return match ($this) {
            self::ALL_USERS => 'All users',
            self::ALL_ACTIVE_MEMBERS => 'All active members',
            self::CHOIR_ADMINS => 'Choir admins',
            self::MUSIC_TEAM => 'Music Team',
            self::EVENTS_TEAM => 'Events Team',
            self::MEMBERSHIP_TEAM => 'Membership Team',
        };
    }

    public function membershipRole(): ?string
    {
        return match ($this) {
            self::CHOIR_ADMINS => 'Admin',
            self::MUSIC_TEAM => 'Music Team',
            self::EVENTS_TEAM => 'Events Team',
            self::MEMBERSHIP_TEAM => 'Membership Team',
            default => null,
        };
    }

    public function includesAllUsers(): bool
    {
        return $this === self::ALL_USERS;
    }
}
