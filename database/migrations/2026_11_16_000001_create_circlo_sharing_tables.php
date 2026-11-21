<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('circlo_sharing_actors', function (Blueprint $table): void {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('student_number', 30)->unique();
            $table->timestampsTz();
        });

        Schema::create('circlo_sharing_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('demo_item_id')->unique()->constrained('circlo_demo_items')->restrictOnDelete();
            $table->foreignId('owner_id')->constrained('circlo_sharing_actors')->restrictOnDelete();
            $table->string('pickup_location');
            $table->text('description');
            $table->timestampsTz();
        });

        Schema::create('circlo_sharing_requests', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('item_id')->constrained('circlo_sharing_items')->restrictOnDelete();
            $table->foreignId('borrower_id')->constrained('circlo_sharing_actors')->restrictOnDelete();
            $table->foreignId('owner_id')->constrained('circlo_sharing_actors')->restrictOnDelete();
            $table->date('starts_on');
            $table->date('ends_on');
            $table->text('message')->nullable();
            $table->enum('status', ['pending', 'accepted', 'rejected', 'cancelled'])->default('pending');
            $table->unsignedInteger('daily_price');
            $table->unsignedBigInteger('total_price');
            $table->text('rejection_reason')->nullable();
            $table->timestampTz('decided_at')->nullable();
            $table->string('demo_key', 60)->nullable()->unique();
            $table->timestampsTz();
            $table->index(['owner_id', 'status']);
            $table->index(['borrower_id', 'status']);
            $table->index(['item_id', 'starts_on', 'ends_on']);
        });

        DB::statement("CREATE UNIQUE INDEX circlo_one_pending_request ON circlo_sharing_requests (borrower_id, item_id) WHERE status = 'pending'");

        Schema::create('circlo_sharing_transactions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('request_id')->unique()->constrained('circlo_sharing_requests')->restrictOnDelete();
            $table->enum('status', ['active', 'completed'])->default('active');
            $table->timestampTz('completed_at')->nullable();
            $table->timestampsTz();
            $table->index('status');
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE circlo_sharing_requests ADD CONSTRAINT circlo_valid_period CHECK (ends_on >= starts_on)');
            DB::statement('ALTER TABLE circlo_sharing_requests ADD CONSTRAINT circlo_distinct_participants CHECK (borrower_id <> owner_id)');
            DB::statement('ALTER TABLE circlo_sharing_requests ADD CONSTRAINT circlo_nonnegative_quote CHECK (daily_price >= 0 AND total_price >= 0)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('circlo_sharing_transactions');
        Schema::dropIfExists('circlo_sharing_requests');
        Schema::dropIfExists('circlo_sharing_items');
        Schema::dropIfExists('circlo_sharing_actors');
    }
};
