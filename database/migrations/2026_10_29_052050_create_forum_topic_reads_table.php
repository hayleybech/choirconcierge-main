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
        Schema::create('forum_topic_reads', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('membership_id');
            $table->foreignId('forum_topic_id')->constrained()->cascadeOnDelete();
            $table->foreignId('last_read_reply_id')->nullable()->constrained('forum_replies')->nullOnDelete();
            $table->string('tenant_id', 191);
            $table->timestamp('read_at');
            $table->timestamps();

            $table->foreign('membership_id')->references('id')->on('memberships')->cascadeOnDelete();
            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnUpdate()->cascadeOnDelete();
            $table->unique(['membership_id', 'forum_topic_id']);
            $table->index(['tenant_id', 'forum_topic_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forum_topic_reads');
    }
};
