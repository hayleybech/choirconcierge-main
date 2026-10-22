<?php

namespace App\Enums;

enum EmailCategory: string
{
    case COMMUNICATIONS = 'communications';
    case SONGS = 'songs';
    case EVENTS = 'events';
    case DOCUMENTS = 'documents';
    case SYSTEM_SECURITY = 'system';
    case PRODUCT_UPDATES = 'product';
    case PROMOTIONS_OFFERS = 'promotions';
    case COMMUNITY_CONTENT = 'community';

    public function label(): string
    {
        return match ($this) {
            self::COMMUNICATIONS => 'Communications',
            self::SONGS => 'Song Created/Updated',
            self::EVENTS => 'Event Created/Updated',
            self::DOCUMENTS => 'Document Created/Updated',
            self::SYSTEM_SECURITY => 'System & Security',
            self::PRODUCT_UPDATES => 'Product Updates',
            self::PROMOTIONS_OFFERS => 'Promotions & Offers',
            self::COMMUNITY_CONTENT => 'Community & Content',
        };
    }

    public function preferenceContext(): string
    {
        return match ($this) {
            self::COMMUNICATIONS, self::SONGS, self::EVENTS, self::DOCUMENTS => 'your choir',
            self::SYSTEM_SECURITY, self::PRODUCT_UPDATES, self::PROMOTIONS_OFFERS, self::COMMUNITY_CONTENT => 'Choir Concierge',
        };
    }

    /**
     * @return list<self>
     */
    public static function productCategories(): array
    {
        return [
            self::SYSTEM_SECURITY,
            self::PRODUCT_UPDATES,
            self::PROMOTIONS_OFFERS,
            self::COMMUNITY_CONTENT,
        ];
    }

    public function isMandatory(): bool
    {
        return in_array($this, [self::COMMUNICATIONS, self::SYSTEM_SECURITY], true);
    }

    public function isEnabledByDefault(): bool
    {
        return ! in_array($this, [self::PROMOTIONS_OFFERS, self::COMMUNITY_CONTENT], true);
    }

    /**
     * @return array{communications: bool, songs: bool, events: bool, documents: bool, system: bool, product: bool, promotions: bool, community: bool}
     */
    public static function defaults(): array
    {
        $preferences = [];

        foreach (self::cases() as $category) {
            $preferences[$category->value] = $category->isEnabledByDefault();
        }

        return $preferences;
    }
}
