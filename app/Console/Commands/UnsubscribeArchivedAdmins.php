<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Mailgun\Mailgun;
use Symfony\Component\Console\Command\Command as CommandAlias;

class UnsubscribeArchivedAdmins extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'unsubscribe:admins';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Removes admins from the admin mailing list in Mailgun if they have not logged in recently.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        if(! App::environment(['local', 'production'])) {
            return CommandAlias::SUCCESS;
        }

        User::whereHas('memberships', fn ($query) => $query
                ->whereHas('roles', fn ($query) => $query->where('name', 'Admin'))
                ->whereDoesntHave('tenant', fn ($query) => $query->active())
            )
            ->select(['email'])
            ->get()
            ->each(fn($user) => Mailgun::create(config('services.mailgun.api_key'))
                ->mailingList()
                ->member()
                ->delete(config('services.mailgun.lists.alerts'), $user->email)
            );

        return CommandAlias::SUCCESS;
    }
}
