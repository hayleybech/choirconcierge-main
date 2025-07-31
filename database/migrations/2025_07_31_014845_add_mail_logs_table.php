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
        Schema::create('mail_logs', function (Blueprint $table) {
            $table->id();
            $table->string('uid', 64); // Only guaranteed to be unique within the same mailbox
            $table->string('from', 254); // Max email length according to intl standard
            $table->string('to', 512); // Arbitrary, assumes several regular-length recipients
            $table->string('cc', 254)->nullable(); // intl std
            $table->string('bcc', 254)->nullable(); // intl std
            $table->string('subject', 128); // Arbitrary
            $table->string('body', 5000); // Arbitrary, but matches the max length for sending broadcasts
            $table->boolean('has_attachments')->default(false);
            $table->timestamp('received_at')->nullable(); // The date the email arrived in the mailbox
            $table->timestamps();
        });

        Schema::create('mail_log_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mail_log_id')->constrained('mail_logs');
            $table->string('status', 64)->nullable(); // Status within Concierge's system
            $table->string('context', 64)->nullable(); // Extra details e.g. the email that was rejected
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
