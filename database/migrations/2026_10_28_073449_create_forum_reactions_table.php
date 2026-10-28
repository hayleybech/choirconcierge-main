<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('forum_reactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('membership_id');
            $table->morphs('reactionable');
            $table->string('emoji', 32);
            $table->string('tenant_id', 191);
            $table->timestamps();

            $table->foreign('membership_id')->references('id')->on('memberships')->cascadeOnDelete();
            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnUpdate()->cascadeOnDelete();
            $table->unique(['membership_id', 'reactionable_type', 'reactionable_id', 'emoji'], 'forum_reactions_unique_member_target_emoji');
            $table->index(['reactionable_type', 'reactionable_id', 'emoji']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forum_reactions');
    }
};
