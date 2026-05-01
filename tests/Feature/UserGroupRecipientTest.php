<?php

namespace Tests\Feature;

use App\Models\UserGroup;
use App\Enums\SingerStatus;
use App\Models\GroupMember;
use App\Models\GroupSender;
use App\Models\User;
use App\Models\Membership;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserGroupRecipientTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_all_recipients_with_singer_status()
    {
        $group = UserGroup::factory()->create();

        // Add a SingerStatus member to the group
        GroupMember::create([
            'group_id' => $group->id,
            'memberable_id' => SingerStatus::MEMBERS->value,
            'memberable_type' => SingerStatus::class,
        ]);

        // Add a regular user to verify it still works
        $user = User::factory()->create();
        $membership = Membership::factory()->for($user)->create(['tenant_id' => 'phpunit']);
        $membership->status()->update(['status' => SingerStatus::MEMBERS]);
        
        // This should not throw "Cannot instantiate enum App\Enums\SingerStatus"
        $recipients = $group->get_all_recipients();
        
        $this->assertTrue($recipients->contains($user));
    }

    public function test_get_all_senders_with_singer_status()
    {
        $group = UserGroup::factory()->create();

        // Add a SingerStatus sender to the group
        GroupSender::create([
            'group_id' => $group->id,
            'sender_id' => SingerStatus::MEMBERS->value,
            'sender_type' => SingerStatus::class,
        ]);

        $user = User::factory()->create();
        $membership = Membership::factory()->for($user)->create(['tenant_id' => 'phpunit']);
        $membership->status()->update(['status' => SingerStatus::MEMBERS]);

        // This should not throw "Cannot instantiate enum App\Enums\SingerStatus"
        $senders = $group->get_all_senders();

        $this->assertTrue($senders->contains($user));
    }
}
