<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Str;

/**
 * Class Notification
 *
 * Columns
 * @property string $id
 * @property array $data
 * @property \Illuminate\Support\Carbon|null $read_at
 * @property \Illuminate\Support\Carbon $created_at
 *
 * Relationships
 * @property Tenant|null $tenant
 *
 * Dynamic
 * @property string|null $tenant_name
 * @property string $subject
 * @property string $body
 * @property string|null $url
 */
class Notification extends DatabaseNotification
{
    protected $visible = ['id', 'tenant_name', 'subject', 'body', 'url', 'read_at', 'created_at'];

    protected $appends = ['tenant_name', 'subject', 'body', 'url'];

    public bool $showTenantName = false;

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    protected function tenantId(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->data['tenant_id'] ?? null,
        );
    }

    protected function tenantName(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->showTenantName ? $this->tenant?->name : null,
        );
    }

    protected function subject(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->data['subject'] ?? 'Notification',
        );
    }

    protected function body(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->formatBody($this->data['body'] ?? ''),
        );
    }

    protected function url(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->data['url'] ?? null,
        );
    }

    private function formatBody(mixed $body): string
    {
        if (! is_string($body) || trim($body) === '') {
            return '';
        }

        $body = preg_replace('/<br\s*\/?\s*>|<\/(?:p|div)>/i', "\n", $body) ?? $body;
        $body = strip_tags($body);
        $lines = preg_split('/\R+/', $body, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        return Str::limit(trim(implode(' ', array_slice($lines, 0, 2))), 140);
    }
}
