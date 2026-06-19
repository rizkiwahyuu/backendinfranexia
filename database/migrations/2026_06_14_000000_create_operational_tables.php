<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table): void {
            $table->id();
            $table->string('asset_code')->unique();
            $table->string('asset_name');
            $table->string('asset_type');
            $table->unsignedTinyInteger('region_id');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('address');
            $table->string('status')->default('active');
            $table->date('installation_date')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('disturbances', function (Blueprint $table): void {
            $table->id();
            $table->string('disturbance_code')->unique();
            $table->foreignId('asset_id')->nullable()->constrained('assets')->nullOnDelete();
            $table->unsignedTinyInteger('region_id');
            $table->string('type');
            $table->unsignedTinyInteger('severity')->default(1);
            $table->string('status')->default('open');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->text('description');
            $table->dateTime('reported_at')->nullable();
            $table->dateTime('resolved_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('pruning_tasks', function (Blueprint $table): void {
            $table->id();
            $table->string('task_code')->unique();
            $table->foreignId('asset_id')->nullable()->constrained('assets')->nullOnDelete();
            $table->unsignedTinyInteger('region_id');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->text('description');
            $table->string('priority')->default('medium');
            $table->string('status')->default('draft');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->date('due_date')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('field_reports', function (Blueprint $table): void {
            $table->id();
            $table->string('report_code')->unique();
            $table->foreignId('task_id')->nullable()->constrained('pruning_tasks')->nullOnDelete();
            $table->foreignId('disturbance_id')->nullable()->constrained('disturbances')->nullOnDelete();
            $table->foreignId('asset_id')->nullable()->constrained('assets')->nullOnDelete();
            $table->foreignId('operator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('report_type');
            $table->text('condition_before')->nullable();
            $table->text('action_taken')->nullable();
            $table->text('condition_after')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->json('attachments')->nullable();
            $table->string('status')->default('submitted');
            $table->text('admin_note')->nullable();
            $table->dateTime('submitted_at')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->timestamps();
        });

        Schema::create('activity_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action');
            $table->string('module')->default('system');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('field_reports');
        Schema::dropIfExists('pruning_tasks');
        Schema::dropIfExists('disturbances');
        Schema::dropIfExists('assets');
    }
};
