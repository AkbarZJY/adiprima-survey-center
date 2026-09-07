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
        Schema::create('surveys', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('category');
            $table->text('description')->nullable();
            $table->string('icon')->default('bi-clipboard-data');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('survey_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained('surveys')->onDelete('cascade');
            $table->string('period_name');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('survey_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained('surveys')->onDelete('cascade');
            $table->string('section', 10); // A, B, C
            $table->string('dimension')->nullable(); // Basic Needs, Individual Contribution, Teamwork, Growth
            $table->integer('question_number');
            $table->string('indicator_title')->nullable();
            $table->text('question_text');
            $table->string('applies_to_employment_status')->nullable(); // e.g. Tetap
            $table->string('applies_to_positions')->nullable(); // e.g. Karu ke atas
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        Schema::create('survey_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained('surveys')->onDelete('cascade');
            $table->foreignId('survey_period_id')->constrained('survey_periods')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('nik')->nullable();
            $table->string('name');
            $table->string('gender')->nullable();
            $table->integer('age')->nullable();
            $table->string('education')->nullable();
            $table->string('employment_status')->nullable();
            $table->string('tenure')->nullable();
            $table->string('department')->nullable();
            $table->string('position')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });

        Schema::create('survey_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_response_id')->constrained('survey_responses')->onDelete('cascade');
            $table->foreignId('question_id')->constrained('survey_questions')->onDelete('cascade');
            $table->unsignedTinyInteger('expectation_score')->nullable(); // 1-4
            $table->unsignedTinyInteger('reality_score')->nullable();     // 1-4
            $table->text('reason_text')->nullable();
            $table->text('text_answer')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_answers');
        Schema::dropIfExists('survey_responses');
        Schema::dropIfExists('survey_questions');
        Schema::dropIfExists('survey_periods');
        Schema::dropIfExists('surveys');
    }
};
