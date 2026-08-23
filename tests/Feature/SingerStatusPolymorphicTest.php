<?php

namespace Tests\Feature;

use App\Models\UserGroup;
use App\Models\Folder;
use App\Models\GroupMember;
use App\Models\GroupSender;
use App\Models\FolderViewer;
use App\Models\FolderEditor;
use App\Enums\SingerStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SingerStatusPolymorphicTest extends TestCase
{
    use RefreshDatabase;

    public function test_group_member_handles_singer_status_gracefully()
    {
        $group = UserGroup::factory()->create();
        
        $member = new GroupMember();
        $member->group_id = $group->id;
        $member->memberable_id = (string)SingerStatus::MEMBERS->value;
        $member->memberable_type = 'App\Enums\SingerStatus';
        $member->save();

        // 1. Verify LAZY loading does not crash (thanks to nullRelation() in the model)
        $memberFromDb = GroupMember::find($member->id);
        $this->assertEquals('App\Enums\SingerStatus', $memberFromDb->memberable_type);
        $this->assertNull($memberFromDb->memberable);

        // 2. Verify EAGER loading is safe when filtered (the logic used in UserGroupController)
        $members = GroupMember::where('memberable_type', '!=', 'App\Enums\SingerStatus')->with('memberable')->get();
        $this->assertEmpty($members);
    }

    public function test_group_sender_handles_singer_status_gracefully()
    {
        $group = UserGroup::factory()->create();
        
        $sender = new GroupSender();
        $sender->group_id = $group->id;
        $sender->sender_id = (string)SingerStatus::MEMBERS->value;
        $sender->sender_type = 'App\Enums\SingerStatus';
        $sender->save();

        $senderFromDb = GroupSender::find($sender->id);
        $this->assertNotNull($senderFromDb);

        $senders = GroupSender::where('sender_type', '!=', 'App\Enums\SingerStatus')->with('sender')->get();
        $this->assertEmpty($senders);
    }

    public function test_folder_viewer_handles_singer_status_gracefully()
    {
        $folder = Folder::create(['title' => 'Test Folder', 'tenant_id' => 'phpunit']);
        
        $viewer = new FolderViewer();
        $viewer->folder_id = $folder->id;
        $viewer->viewer_id = (string)SingerStatus::MEMBERS->value;
        $viewer->viewer_type = 'App\Enums\SingerStatus';
        $viewer->save();

        $viewerFromDb = FolderViewer::find($viewer->id);
        $this->assertNotNull($viewerFromDb);

        $viewers = FolderViewer::where('viewer_type', '!=', 'App\Enums\SingerStatus')->with('viewer')->get();
        $this->assertEmpty($viewers);
    }

    public function test_folder_editor_handles_singer_status_gracefully()
    {
        $folder = Folder::create(['title' => 'Test Folder', 'tenant_id' => 'phpunit']);
        
        $editor = new FolderEditor();
        $editor->folder_id = $folder->id;
        $editor->editor_id = (string)SingerStatus::MEMBERS->value;
        $editor->editor_type = 'App\Enums\SingerStatus';
        $editor->save();

        $editorFromDb = FolderEditor::find($editor->id);
        $this->assertNotNull($editorFromDb);

        $editors = FolderEditor::where('editor_type', '!=', 'App\Enums\SingerStatus')->with('editor')->get();
        $this->assertEmpty($editors);
    }
}
