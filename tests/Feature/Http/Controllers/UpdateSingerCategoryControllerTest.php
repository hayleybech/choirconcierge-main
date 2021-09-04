<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Singer;
use App\Models\SingerCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\UpdateSingerCategoryController
 */
class UpdateSingerCategoryControllerTest extends TestCase
{
	use RefreshDatabase;

	/**
	 * @test
	 */
	public function invoke_redirects_to_index(): void
	{
		$this->actingAs($this->createUserWithRole('Membership Team'));

		$singer = Singer::factory()->create();

		$new_category_id = SingerCategory::inRandomOrder()->value('id');
		$response = $this->get(
			the_tenant_route('singers.categories.update', [$singer]) . '?move_category=' . $new_category_id,
			[
				'move_category' => $new_category_id,
			],
		);

		$response->assertSessionHasNoErrors();
		$response->assertRedirect();
		$this->assertDatabaseHas('singers', [
			'id' => $singer->id,
			'singer_category_id' => $new_category_id,
		]);
	}
}
