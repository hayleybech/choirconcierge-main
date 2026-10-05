<?php

namespace App\Enums;

enum SingerStatus: string
{
    case PROSPECTS = 'prospects';
    case ARCHIVED_PROSPECTS = 'archived-prospects';
    case MEMBERS = 'members';
    case INACTIVE_MEMBERS = 'inactive-members';
    case ARCHIVED_MEMBERS = 'archived-members';

    public function label(): string
    {
        return match ($this) {
            self::PROSPECTS => 'Prospects',
            self::ARCHIVED_PROSPECTS => 'Archived Prospects',
            self::MEMBERS => 'Members',
            self::INACTIVE_MEMBERS => 'Inactive Members',
            self::ARCHIVED_MEMBERS => 'Former Members',
        };
    }

    public function colour(): string
    {
        return match ($this) {
            self::PROSPECTS => 'amber-500',
            self::ARCHIVED_PROSPECTS => 'amber-700',
            self::MEMBERS => 'emerald-500',
            self::INACTIVE_MEMBERS => 'emerald-700',
            self::ARCHIVED_MEMBERS => 'blue-500',
        };
    }

    public function textColour(): string
    {
        return "text-{$this->colour()}";
    }

    public function icon(): string
    {
        return 'circle';
    }

    public static function fromName(string $name): ?self
    {
        return match ($name) {
            'Prospects' => self::PROSPECTS,
            'Archived Prospects' => self::ARCHIVED_PROSPECTS,
            'Members' => self::MEMBERS,
            'Inactive Members' => self::INACTIVE_MEMBERS,
            'Former Members' => self::ARCHIVED_MEMBERS,
            'Archived Members' => self::ARCHIVED_MEMBERS,
            default => self::tryFrom(str($name)->slug()->toString()),
        };
    }
}
