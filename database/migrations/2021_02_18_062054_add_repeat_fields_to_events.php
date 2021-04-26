<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRepeatFieldsToEvents extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('events', static function (Blueprint $table) {
            $table->boolean('is_repeating')->default(false);
            $table->integer('repeat_frequency_amount')->default(1);
            $table->string('repeat_frequency_unit')->nullable();
            $table->timestamp('repeat_until')->nullable();

            $table->unsignedInteger('repeat_parent_id')->nullable();
            $table->foreign('repeat_parent_id')->references('id')->on('events')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', static function (Blueprint $table) {
            $table->dropForeign('events_repeat_parent_id_foreign');
            $table->dropColumn('repeat_parent_id');

            $table->dropColumn('repeat_until');
            $table->dropColumn('repeat_frequency_unit');
            $table->dropColumn('repeat_frequency_amount');
            $table->dropColumn('is_repeating');
        });
    }
}
