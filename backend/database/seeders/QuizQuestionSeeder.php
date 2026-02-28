<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Quiz;
use App\Models\QuizQuestion;

class QuizQuestionSeeder extends Seeder
{
    public function run(): void
    {
        // Get first quiz (Quadratic Equations - Basic)
        $quiz1 = Quiz::where('title', 'Quadratic Equations - Basic Concepts')->first();
        
        if ($quiz1) {
            $questions = [
                [
                    'question_type' => 'single_correct',
                    'question_text' => 'What is the standard form of a quadratic equation?',
                    'option_a' => 'ax + b = 0',
                    'option_b' => 'ax² + bx + c = 0',
                    'option_c' => 'ax³ + bx² + cx + d = 0',
                    'option_d' => 'ax² + b = 0',
                    'correct_answers' => ['B'],
                    'marks' => 2,
                    'negative_marks' => 0.5,
                    'explanation' => 'The standard form of a quadratic equation is ax² + bx + c = 0, where a ≠ 0.',
                    'sort_order' => 1,
                ],
                [
                    'question_type' => 'single_correct',
                    'question_text' => 'Solve for x: x² - 5x + 6 = 0',
                    'option_a' => 'x = 1, 6',
                    'option_b' => 'x = 2, 3',
                    'option_c' => 'x = -2, -3',
                    'option_d' => 'x = 5, 6',
                    'correct_answers' => ['B'],
                    'marks' => 2,
                    'negative_marks' => 0.5,
                    'explanation' => 'Factoring: (x-2)(x-3) = 0, so x = 2 or x = 3',
                    'sort_order' => 2,
                ],
                [
                    'question_type' => 'multiple_correct',
                    'question_text' => 'Which of the following are quadratic equations?',
                    'option_a' => '2x² + 3x - 5 = 0',
                    'option_b' => 'x + 5 = 0',
                    'option_c' => 'x² - 4 = 0',
                    'option_d' => 'x³ + 2x = 0',
                    'correct_answers' => ['A', 'C'],
                    'marks' => 3,
                    'negative_marks' => 1,
                    'explanation' => 'Options A and C are quadratic equations (degree 2). B is linear and D is cubic.',
                    'sort_order' => 3,
                ],
                [
                    'question_type' => 'true_false',
                    'question_text' => 'Every quadratic equation has two real roots.',
                    'option_a' => 'True',
                    'option_b' => 'False',
                    'option_c' => null,
                    'option_d' => null,
                    'correct_answers' => ['B'],
                    'marks' => 1,
                    'negative_marks' => 0.25,
                    'explanation' => 'False. A quadratic equation can have two real roots, one real root, or two complex roots depending on the discriminant.',
                    'sort_order' => 4,
                ],
                [
                    'question_type' => 'single_correct',
                    'question_text' => 'What is the discriminant of the equation 2x² + 3x - 5 = 0?',
                    'option_a' => '9',
                    'option_b' => '29',
                    'option_c' => '49',
                    'option_d' => '19',
                    'correct_answers' => ['C'],
                    'marks' => 2,
                    'negative_marks' => 0.5,
                    'explanation' => 'Discriminant = b² - 4ac = 3² - 4(2)(-5) = 9 + 40 = 49',
                    'sort_order' => 5,
                ],
            ];

            foreach ($questions as $question) {
                QuizQuestion::create(array_merge($question, ['quiz_id' => $quiz1->id]));
            }

            $quiz1->updateTotalQuestions();
        }

        // Get second quiz (Trigonometry)
        $quiz2 = Quiz::where('title', 'Introduction to Trigonometry')->first();
        
        if ($quiz2) {
            $questions = [
                [
                    'question_type' => 'single_correct',
                    'question_text' => 'What is the value of sin 90°?',
                    'option_a' => '0',
                    'option_b' => '1',
                    'option_c' => '1/2',
                    'option_d' => '√3/2',
                    'correct_answers' => ['B'],
                    'marks' => 1,
                    'negative_marks' => 0.25,
                    'explanation' => 'sin 90° = 1',
                    'sort_order' => 1,
                ],
                [
                    'question_type' => 'single_correct',
                    'question_text' => 'What is the value of cos 0°?',
                    'option_a' => '0',
                    'option_b' => '1',
                    'option_c' => '-1',
                    'option_d' => '1/2',
                    'correct_answers' => ['B'],
                    'marks' => 1,
                    'negative_marks' => 0.25,
                    'explanation' => 'cos 0° = 1',
                    'sort_order' => 2,
                ],
                [
                    'question_type' => 'single_correct',
                    'question_text' => 'If sin θ = 3/5, what is cos θ? (θ is acute)',
                    'option_a' => '4/5',
                    'option_b' => '3/4',
                    'option_c' => '5/3',
                    'option_d' => '5/4',
                    'correct_answers' => ['A'],
                    'marks' => 2,
                    'negative_marks' => 0.5,
                    'explanation' => 'Using Pythagoras: cos θ = √(1 - sin²θ) = √(1 - 9/25) = √(16/25) = 4/5',
                    'sort_order' => 3,
                ],
            ];

            foreach ($questions as $question) {
                QuizQuestion::create(array_merge($question, ['quiz_id' => $quiz2->id]));
            }

            $quiz2->updateTotalQuestions();
        }

        // Get third quiz (Electricity)
        $quiz3 = Quiz::where('title', 'Ohm\'s Law and Resistance')->first();
        
        if ($quiz3) {
            $questions = [
                [
                    'question_type' => 'single_correct',
                    'question_text' => 'Ohm\'s law states that V = ?',
                    'option_a' => 'I/R',
                    'option_b' => 'IR',
                    'option_c' => 'I + R',
                    'option_d' => 'R/I',
                    'correct_answers' => ['B'],
                    'marks' => 1,
                    'negative_marks' => 0.25,
                    'explanation' => 'Ohm\'s law: V = IR, where V is voltage, I is current, and R is resistance.',
                    'sort_order' => 1,
                ],
                [
                    'question_type' => 'single_correct',
                    'question_text' => 'If a resistor of 10Ω carries a current of 2A, what is the voltage across it?',
                    'option_a' => '5V',
                    'option_b' => '12V',
                    'option_c' => '20V',
                    'option_d' => '8V',
                    'correct_answers' => ['C'],
                    'marks' => 2,
                    'negative_marks' => 0.5,
                    'explanation' => 'V = IR = 2A × 10Ω = 20V',
                    'sort_order' => 2,
                ],
            ];

            foreach ($questions as $question) {
                QuizQuestion::create(array_merge($question, ['quiz_id' => $quiz3->id]));
            }

            $quiz3->updateTotalQuestions();
        }
    }
}
