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
        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('quizzes')->onDelete('cascade');
            $table->enum('question_type', ['single_correct', 'multiple_correct', 'true_false'])->default('single_correct');
            $table->text('question_text');
            $table->text('option_a');
            $table->text('option_b');
            $table->text('option_c')->nullable();
            $table->text('option_d')->nullable();
            $table->json('correct_answers')->comment('JSON array of correct options, e.g., ["A"] or ["A","C"]');
            $table->decimal('marks', 8, 2)->default(1.00);
            $table->decimal('negative_marks', 8, 2)->default(0.00);
            $table->text('explanation')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index('quiz_id');
            $table->index('question_type');
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_questions');
    }
};
