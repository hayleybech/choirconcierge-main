<?php

namespace App\Enums;

enum SingerStatus: string
{
    case MEMBERS = 'members';
    case PROSPECTS = 'prospects';
    case ARCHIVED_PROSPECTS = 'archived-prospects';
    case ARCHIVED_MEMBERS = 'archived-members';

    public function label(): string
    {
        return match ($this) {
            self::MEMBERS => 'Members',
            self::PROSPECTS => 'Prospects',
            self::ARCHIVED_PROSPECTS => 'Archived Prospects',
            self::ARCHIVED_MEMBERS => 'Archived Members',
        };
    }

    public function colour(): string
    {
        return match ($this) {
            self::MEMBERS => 'emerald-500',
            self::PROSPECTS => 'amber-500',
            self::ARCHIVED_PROSPECTS => 'amber-700',
            self::ARCHIVED_MEMBERS => 'emerald-700',
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
            'Members' => self::MEMBERS,
            'Prospects' => self::PROSPECTS,
            'Archived Prospects' => self::ARCHIVED_PROSPECTS,
            'Archived Members' => self::ARCHIVED_MEMBERS,
            default => self::tryFrom(str($name)->slug()->toString()),
        };
    }
}
