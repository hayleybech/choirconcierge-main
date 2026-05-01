<?php

namespace Database\Seeders\Dummy;

use App\Models\MailLog;
use App\Models\MailLogEvent;
use App\Models\UserGroup;
use Illuminate\Database\Seeder;

class DummyMailLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenantId = 'test';
        $groups = UserGroup::where('tenant_id', $tenantId)->get();
        if ($groups->isEmpty()) {
            $groups = UserGroup::factory()->count(3)->create(['tenant_id' => $tenantId]);
        }

        // Scenario 1: Successful broadcast to group
        $log1 = MailLog::factory()->create([
            'subject' => 'Weekly Rehearsal Update',
        ]);
        $log1->tenants()->attach($tenantId);
        $this->createEvents($log1, [
            ['status' => 'received'],
            ['status' => 'pending'],
            ['status' => 'group-found', 'user_group_id' => $groups->random()->id],
            ['status' => 'clones-sent'],
            ['status' => 'opened', 'context' => 'member1@example.com'],
            ['status' => 'opened', 'context' => 'member2@example.com'],
        ]);

        // Scenario 2: Group not found
        $log2 = MailLog::factory()->create([
            'subject' => 'Inquiry for Non-existent Group',
        ]);
        $this->createEvents($log2, [
            ['status' => 'received'],
            ['status' => 'pending'],
            ['status' => 'group-not-found', 'context' => 'unknown-group@concierge.com'],
        ]);

        // Scenario 3: Rejected sender
        $log3 = MailLog::factory()->create([
            'subject' => 'Unauthorized Post',
        ]);
        $log3->tenants()->attach($tenantId);
        $this->createEvents($log3, [
            ['status' => 'received'],
            ['status' => 'pending'],
            ['status' => 'group-found', 'user_group_id' => $groups->random()->id],
            ['status' => 'rejected-sender', 'context' => 'Some Group'],
        ]);

        // Scenario 4: Group empty
        $log4 = MailLog::factory()->create([
            'subject' => 'Message to Empty Group',
        ]);
        $log4->tenants()->attach($tenantId);
        $this->createEvents($log4, [
            ['status' => 'received'],
            ['status' => 'pending'],
            ['status' => 'group-found', 'user_group_id' => $groups->random()->id],
            ['status' => 'group-empty', 'user_group_id' => $groups->random()->id],
        ]);

        // Scenario 5: Malformed recipient & notification sent
        $log5 = MailLog::factory()->create([
            'subject' => 'Mixed Results',
        ]);
        $this->createEvents($log5, [
            ['status' => 'received'],
            ['status' => 'pending'],
            ['status' => 'malformed-recipient', 'context' => 'bad-email-at-domain.com'],
            ['status' => 'notification-sent'],
        ]);

        // Scenario 6: Clone failed & Send failed
        $log6 = MailLog::factory()->create([
            'subject' => 'System Errors',
        ]);
        $log6->tenants()->attach($tenantId);
        $this->createEvents($log6, [
            ['status' => 'received'],
            ['status' => 'pending'],
            ['status' => 'group-found', 'user_group_id' => $groups->random()->id],
            ['status' => 'clone-failed', 'context' => 'failed-recipient@example.com'],
            ['status' => 'send-failed', 'context' => 'Critical SMTP error'],
        ]);

        // Scenario 7: Inbound message
        $log7 = MailLog::factory()->create([
            'subject' => 'Direct Message to Inbox',
        ]);
        $this->createEvents($log7, [
            ['status' => 'received'],
        ]);
    }

    private function createEvents(MailLog $log, array $events): void
    {
        foreach ($events as $index => $eventData) {
            MailLogEvent::create(array_merge([
                'mail_log_id' => $log->id,
                'created_at' => now()->addMinutes($index),
                'updated_at' => now()->addMinutes($index),
            ], $eventData));
        }
    }
}
