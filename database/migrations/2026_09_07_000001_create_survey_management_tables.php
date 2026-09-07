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
        // 1. Survey Dimensions / Categories
        Schema::create('survey_dimensions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable();
            $table->text('description')->nullable();
            $table->string('color')->default('#2563EB');
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // 2. Question Templates (Question Bank)
        Schema::create('question_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dimension_id')->nullable()->constrained('survey_dimensions')->nullOnDelete();
            $table->string('indicator_title')->nullable();
            $table->text('question_text');
            $table->string('question_type')->default('dual_rating'); // dual_rating, single_rating, multiple_choice, essay
            $table->integer('rating_scale')->default(4); // 4, 5, 10
            $table->boolean('require_reason_on_low_score')->default(false);
            $table->integer('low_score_threshold')->default(2);
            $table->json('options_json')->nullable();
            $table->string('applies_to_employment_status')->nullable();
            $table->string('applies_to_positions')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // 3. Update survey_questions to support dimensions, templates, types, and conditional logic
        Schema::table('survey_questions', function (Blueprint $table) {
            $table->foreignId('dimension_id')->nullable()->after('survey_id')->constrained('survey_dimensions')->nullOnDelete();
            $table->foreignId('question_template_id')->nullable()->after('dimension_id')->constrained('question_templates')->nullOnDelete();
            $table->string('question_type')->default('dual_rating')->after('question_text');
            $table->integer('rating_scale')->default(4)->after('question_type');
            $table->boolean('require_reason_on_low_score')->default(false)->after('rating_scale');
            $table->integer('low_score_threshold')->default(2)->after('require_reason_on_low_score');
            $table->json('options_json')->nullable()->after('low_score_threshold');
        });

        // 4. Update surveys table to have direct start_date and end_date if needed
        Schema::table('surveys', function (Blueprint $table) {
            $table->date('start_date')->nullable()->after('icon');
            $table->date('end_date')->nullable()->after('start_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surveys', function (Blueprint $table) {
            $table->dropColumn(['start_date', 'end_date']);
        });

        Schema::table('survey_questions', function (Blueprint $table) {
            $table->dropForeign(['dimension_id']);
            $table->dropForeign(['question_template_id']);
            $table->dropColumn([
                'dimension_id',
                'question_template_id',
                'question_type',
                'rating_scale',
                'require_reason_on_low_score',
                'low_score_threshold',
                'options_json'
            ]);
        });

        Schema::dropIfExists('question_templates');
        Schema::dropIfExists('survey_dimensions');
    }
};
