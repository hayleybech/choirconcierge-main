<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\Ensemble;
use App\Models\Folder;
use App\Models\Membership;
use App\Models\Role;
use App\Models\User;
use App\Models\VoicePart;
use Inertia\Testing\AssertableInertia;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FolderEnsembleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Setup necessary roles and permissions if they don't exist
        Role::updateOrCreate(['name' => 'Music Team'], ['abilities' => ['folders_view', 'folders_update', 'folders_create', 'folders_delete', 'documents_view', 'documents_create', 'documents_delete']]);
        Role::updateOrCreate(['name' => 'Singer'], ['abilities' => ['folders_view', 'documents_view']]);
        Role::updateOrCreate(['name' => 'User'], ['abilities' => []]);
    }

    private function createSingerWithEnsemble(Ensemble $ensemble = null): User {
        $membership = Membership::factory()->create();
        $membership->roles()->attach([Role::where('name', 'Singer')->valueOrFail('id')]);
        
        if ($ensemble) {
            $voicePart = VoicePart::factory()->create();
            $membership->enrolments()->create([
                'ensemble_id' => $ensemble->id,
                'voice_part_id' => $voicePart->id
            ]);
        }
        
        return $membership->user;
    }

    private function createMusicTeamUser(): User {
        $membership = Membership::factory()->create();
        $membership->roles()->attach([Role::where('name', 'Music Team')->valueOrFail('id')]);
        return $membership->user;
    }

    public function test_shows_folders_with_no_ensembles_to_all_users()
    {
        $folder = Folder::factory()->create();
        $user = $this->createSingerWithEnsemble();

        $this->actingAs($user)
            ->get(the_tenant_route('folders.index'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->has('folders', fn (AssertableInertia $folders) => $folders
                    ->where('0.id', $folder->id)
                    ->etc()
                )
            );
    }

    public function test_hides_folders_with_ensembles_from_users_not_in_those_ensembles()
    {
        $ensemble = Ensemble::factory()->create();
        $folder = Folder::factory()->create();
        $folder->ensembles()->attach($ensemble);
        
        $user = $this->createSingerWithEnsemble(); // Not in any ensemble

        $this->actingAs($user)
            ->get(the_tenant_route('folders.index'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->has('folders', 0)
            );
    }

    public function test_shows_folders_with_ensembles_to_members_of_those_ensembles()
    {
        $ensemble = Ensemble::factory()->create();
        $folder = Folder::factory()->create();
        $folder->ensembles()->attach($ensemble);
        
        $user = $this->createSingerWithEnsemble($ensemble);

        $this->actingAs($user)
            ->get(the_tenant_route('folders.index'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->has('folders', 1)
                ->where('folders.0.id', $folder->id)
            );
    }

    public function test_shows_all_folders_to_Music_Team_users_regardless_of_ensembles()
    {
        $ensemble = Ensemble::factory()->create();
        $folder = Folder::factory()->create();
        $folder->ensembles()->attach($ensemble);
        
        $user = $this->createMusicTeamUser();

        $this->actingAs($user)
            ->get(the_tenant_route('folders.index'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->has('folders', 1)
                ->where('folders.0.id', $folder->id)
            );
    }

    public function test_syncs_ensembles_when_creating_a_folder()
    {
        $ensemble = Ensemble::factory()->create();
        $user = $this->createMusicTeamUser();

        $this->actingAs($user)
            ->post(the_tenant_route('folders.store'), [
                'title' => 'New Ensemble Folder',
                'ensembles' => [$ensemble->id]
            ])
            ->assertRedirect(the_tenant_route('folders.index'));

        $folder = Folder::where('title', 'New Ensemble Folder')->first();
        $this->assertCount(1, $folder->ensembles);
        $this->assertEquals($ensemble->id, $folder->ensembles->first()->id);
    }

    public function test_syncs_ensembles_when_updating_a_folder()
    {
        $ensemble1 = Ensemble::factory()->create();
        $ensemble2 = Ensemble::factory()->create();
        $folder = Folder::factory()->create();
        $folder->ensembles()->attach($ensemble1);
        
        $user = $this->createMusicTeamUser();

        $this->actingAs($user)
            ->put(the_tenant_route('folders.update', [$folder]), [
                'title' => 'Updated Folder',
                'ensembles' => [$ensemble2->id]
            ])
            ->assertRedirect(the_tenant_route('folders.index'));

        $folder->refresh();
        $this->assertCount(1, $folder->ensembles);
        $this->assertEquals($ensemble2->id, $folder->ensembles->first()->id);
    }

    public function test_restricts_document_access_based_on_folder_ensembles()
    {
        $ensemble = Ensemble::factory()->create();
        $folder = Folder::factory()->create();
        $folder->ensembles()->attach($ensemble);
        
        $document = Document::factory()->create(['folder_id' => $folder->id]);
        
        $userNotInEnsemble = $this->createSingerWithEnsemble();
        $userInEnsemble = $this->createSingerWithEnsemble($ensemble);

        // User not in ensemble cannot view document
        $this->actingAs($userNotInEnsemble)
            ->get(the_tenant_route('folders.documents.show', [$folder, $document]))
            ->assertForbidden();

        // User in ensemble can view document
        $this->actingAs($userInEnsemble)
            ->get(the_tenant_route('folders.documents.show', [$folder, $document]))
            ->assertOk();
    }
}
