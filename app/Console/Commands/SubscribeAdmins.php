<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Mailgun\Mailgun;
use Symfony\Component\Console\Command\Command as CommandAlias;

class SubscribeAdmins extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscribe:admins';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add all admins to the admin mailing list in Mailgun';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        Mailgun::create(config('services.mailgun.api_key'))
            ->mailingList()
            ->member()
            ->createMultiple(
                config('services.mailgun.lists.alerts'),
                User::whereHas('memberships.roles', function ($query) {
                    $query->where('name', 'Admin');
                })->select(['email', 'first_name', 'last_name'])
                    ->get()
                    ->map(fn($user) => [
                        'address' => $user->email,
                        'name' => $user->first_name . ' ' . $user->last_name,
                    ])
                    ->all(),
                true
            );

        return CommandAlias::SUCCESS;
    }
}
