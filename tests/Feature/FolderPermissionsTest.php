<?php

use App\Models\Ensemble;
use App\Models\Folder;
use App\Models\Membership;
use App\Models\Role;
use App\Models\User;
use App\Models\VoicePart;
use App\Models\SingerStatus;
use function Pest\Laravel\actingAs;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::updateOrCreate(['name' => 'Music Team'], ['abilities' => ['folders_view', 'folders_update', 'folders_create', 'folders_delete']]);
    Role::updateOrCreate(['name' => 'Singer'], ['abilities' => ['folders_view']]);
    Role::updateOrCreate(['name' => 'Board'], ['abilities' => ['folders_view']]);
    Role::updateOrCreate(['name' => 'User'], ['abilities' => []]);
});

function createSinger(array $roles = ['Singer'], ?Ensemble $ensemble = null, ?SingerStatus $status = null): User {
    if (!$status) {
        $status = SingerStatus::updateOrCreate(['name' => 'Members']);
    }
    $membership = Membership::factory()->create();
    $membership->statuses()->attach($status);
    $membership->roles()->attach(Role::whereIn('name', $roles)->pluck('id'));
    
    if ($ensemble) {
        $voicePart = VoicePart::factory()->create();
        $membership->enrolments()->create([
            'ensemble_id' => $ensemble->id,
            'voice_part_id' => $voicePart->id
        ]);
    }
    
    return $membership->user;
}

it('allows specific users to view folder', function () {
    $folder = Folder::factory()->create();
    $allowedUser = createSinger();
    $disallowedUser = createSinger();
    
    $folder->viewer_users()->attach($allowedUser->id);
    
    actingAs($allowedUser)
        ->get(the_tenant_route('folders.index'))
        ->assertInertia(fn ($page) => $page->has('folders', 1));
        
    actingAs($disallowedUser)
        ->get(the_tenant_route('folders.index'))
        ->assertInertia(fn ($page) => $page->has('folders', 0));
});

it('allows specific roles to view folder', function () {
    $folder = Folder::factory()->create();
    $boardRole = Role::where('name', 'Board')->first();
    $boardUser = createSinger(['Board']);
    $singerUser = createSinger(['Singer']);
    
    $folder->viewer_roles()->attach($boardRole->id);
    
    actingAs($boardUser)
        ->get(the_tenant_route('folders.index'))
        ->assertInertia(fn ($page) => $page->has('folders', 1));
        
    actingAs($singerUser)
        ->get(the_tenant_route('folders.index'))
        ->assertInertia(fn ($page) => $page->has('folders', 0));
});

it('filters viewers by ensemble', function () {
    $ensemble = Ensemble::factory()->create();
    $folder = Folder::factory()->create();
    $folder->ensembles()->attach($ensemble);
    
    $allowedRole = Role::where('name', 'Singer')->first();
    $userInEnsemble = createSinger(['Singer'], $ensemble);
    $userNotInEnsemble = createSinger(['Singer']);
    
    $folder->viewer_roles()->attach($allowedRole->id);
    
    actingAs($userInEnsemble)
        ->get(the_tenant_route('folders.index'))
        ->assertInertia(fn ($page) => $page->has('folders', 1));
        
    actingAs($userNotInEnsemble)
        ->get(the_tenant_route('folders.index'))
        ->assertInertia(fn ($page) => $page->has('folders', 0));
});

it('allows specific users to edit folder', function () {
    $folder = Folder::factory()->create();
    $allowedEditor = createSinger(['Music Team']);
    $disallowedEditor = createSinger(['Music Team']);
    
    $folder->editor_users()->attach($allowedEditor->id);
    
    actingAs($allowedEditor)
        ->get(the_tenant_route('folders.edit', $folder))
        ->assertSuccessful();
        
    actingAs($disallowedEditor)
        ->get(the_tenant_route('folders.edit', $folder))
        ->assertForbidden();
});

it('allows admins to edit everything regardless of specific editors', function () {
    Role::updateOrCreate(['name' => 'Admin'], ['abilities' => ['folders_update']]);
    $adminUser = createSinger(['Admin']);
    
    $otherUser = createSinger(['Music Team']);
    $folder = Folder::factory()->create();
    $folder->editor_users()->attach($otherUser->id);
    
    actingAs($adminUser)
        ->get(the_tenant_route('folders.edit', $folder))
        ->assertSuccessful();
});

it('syncs all permission types when creating folder', function () {
    $user = createSinger(['Music Team']);
    $viewerUser = User::factory()->create();
    $editorRole = Role::where('name', 'Singer')->first();
    $voicePart = VoicePart::factory()->create();
    $status = SingerStatus::factory()->create();
    
    actingAs($user)
        ->post(the_tenant_route('folders.store'), [
            'title' => 'Permitted Folder',
            'viewer_users' => [$viewerUser->id],
            'editor_roles' => [$editorRole->id],
            'viewer_voice_parts' => [$voicePart->id],
            'editor_singer_statuses' => [$status->id],
        ])
        ->assertRedirect(the_tenant_route('folders.index'));
        
    $folder = Folder::where('title', 'Permitted Folder')->first();
    expect($folder->viewer_users)->toHaveCount(1);
    expect($folder->viewer_users->first()->id)->toBe($viewerUser->id);
    expect($folder->editor_roles)->toHaveCount(1);
    expect($folder->editor_roles->first()->id)->toBe($editorRole->id);
    expect($folder->viewer_voice_parts)->toHaveCount(1);
    expect($folder->viewer_voice_parts->first()->id)->toBe($voicePart->id);
    expect($folder->editor_singer_statuses)->toHaveCount(1);
    expect($folder->editor_singer_statuses->first()->id)->toBe($status->id);
});
