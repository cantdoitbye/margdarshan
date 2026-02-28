<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $questions = [
            // Mathematics Questions (10 questions)
            ['subject' => 'Mathematics', 'question' => 'What is the value of π (pi) approximately?', 'options' => json_encode(['2.14', '3.14', '4.14', '5.14']), 'correct_answer' => '3.14', 'difficulty' => 'easy'],
            ['subject' => 'Mathematics', 'question' => 'What is the derivative of x²?', 'options' => json_encode(['x', '2x', 'x²', '2x²']), 'correct_answer' => '2x', 'difficulty' => 'medium'],
            ['subject' => 'Mathematics', 'question' => 'What is the Pythagorean theorem?', 'options' => json_encode(['a² + b² = c²', 'a + b = c', 'a² - b² = c²', 'a × b = c']), 'correct_answer' => 'a² + b² = c²', 'difficulty' => 'easy'],
            ['subject' => 'Mathematics', 'question' => 'What is 15% of 200?', 'options' => json_encode(['20', '25', '30', '35']), 'correct_answer' => '30', 'difficulty' => 'easy'],
            ['subject' => 'Mathematics', 'question' => 'Solve: 2x + 5 = 15', 'options' => json_encode(['x = 5', 'x = 10', 'x = 7.5', 'x = 20']), 'correct_answer' => 'x = 5', 'difficulty' => 'medium'],
            ['subject' => 'Mathematics', 'question' => 'What is the area of a circle with radius 7?', 'options' => json_encode(['154 sq units', '49 sq units', '98 sq units', '196 sq units']), 'correct_answer' => '154 sq units', 'difficulty' => 'medium'],
            ['subject' => 'Mathematics', 'question' => 'What is the sum of angles in a triangle?', 'options' => json_encode(['90°', '180°', '270°', '360°']), 'correct_answer' => '180°', 'difficulty' => 'easy'],
            ['subject' => 'Mathematics', 'question' => 'What is the square root of 169?', 'options' => json_encode(['11', '12', '13', '14']), 'correct_answer' => '13', 'difficulty' => 'easy'],
            ['subject' => 'Mathematics', 'question' => 'If a = 3 and b = 4, what is a² + b²?', 'options' => json_encode(['7', '12', '25', '49']), 'correct_answer' => '25', 'difficulty' => 'medium'],
            ['subject' => 'Mathematics', 'question' => 'What is 0.5 as a fraction?', 'options' => json_encode(['1/4', '1/2', '1/3', '2/3']), 'correct_answer' => '1/2', 'difficulty' => 'easy'],
            
            // Physics Questions (10 questions)
            ['subject' => 'Physics', 'question' => 'What is the SI unit of force?', 'options' => json_encode(['Joule', 'Newton', 'Watt', 'Pascal']), 'correct_answer' => 'Newton', 'difficulty' => 'easy'],
            ['subject' => 'Physics', 'question' => 'What is the speed of light in vacuum?', 'options' => json_encode(['3 × 10⁸ m/s', '3 × 10⁶ m/s', '3 × 10⁷ m/s', '3 × 10⁹ m/s']), 'correct_answer' => '3 × 10⁸ m/s', 'difficulty' => 'medium'],
            ['subject' => 'Physics', 'question' => 'What is Newton\'s first law of motion?', 'options' => json_encode(['F = ma', 'Law of Inertia', 'Action-Reaction', 'E = mc²']), 'correct_answer' => 'Law of Inertia', 'difficulty' => 'easy'],
            ['subject' => 'Physics', 'question' => 'What is the unit of electric current?', 'options' => json_encode(['Volt', 'Ampere', 'Ohm', 'Watt']), 'correct_answer' => 'Ampere', 'difficulty' => 'easy'],
            ['subject' => 'Physics', 'question' => 'What is the acceleration due to gravity on Earth?', 'options' => json_encode(['9.8 m/s²', '10 m/s²', '8.9 m/s²', '11 m/s²']), 'correct_answer' => '9.8 m/s²', 'difficulty' => 'easy'],
            ['subject' => 'Physics', 'question' => 'What is Ohm\'s Law?', 'options' => json_encode(['V = IR', 'P = VI', 'E = mc²', 'F = ma']), 'correct_answer' => 'V = IR', 'difficulty' => 'medium'],
            ['subject' => 'Physics', 'question' => 'What type of energy does a moving object have?', 'options' => json_encode(['Potential', 'Kinetic', 'Thermal', 'Chemical']), 'correct_answer' => 'Kinetic', 'difficulty' => 'easy'],
            ['subject' => 'Physics', 'question' => 'What is the SI unit of power?', 'options' => json_encode(['Joule', 'Newton', 'Watt', 'Pascal']), 'correct_answer' => 'Watt', 'difficulty' => 'easy'],
            ['subject' => 'Physics', 'question' => 'What is the formula for kinetic energy?', 'options' => json_encode(['½mv²', 'mgh', 'mc²', 'Fd']), 'correct_answer' => '½mv²', 'difficulty' => 'medium'],
            ['subject' => 'Physics', 'question' => 'What is the principle of conservation of energy?', 'options' => json_encode(['Energy can be created', 'Energy can be destroyed', 'Energy cannot be created or destroyed', 'Energy always increases']), 'correct_answer' => 'Energy cannot be created or destroyed', 'difficulty' => 'medium'],
            
            // Chemistry Questions (10 questions)
            ['subject' => 'Chemistry', 'question' => 'What is the chemical symbol for water?', 'options' => json_encode(['H2O', 'CO2', 'O2', 'H2']), 'correct_answer' => 'H2O', 'difficulty' => 'easy'],
            ['subject' => 'Chemistry', 'question' => 'What is the atomic number of Carbon?', 'options' => json_encode(['4', '6', '8', '12']), 'correct_answer' => '6', 'difficulty' => 'easy'],
            ['subject' => 'Chemistry', 'question' => 'What is the pH of pure water?', 'options' => json_encode(['0', '7', '14', '10']), 'correct_answer' => '7', 'difficulty' => 'easy'],
            ['subject' => 'Chemistry', 'question' => 'What is the most abundant gas in Earth\'s atmosphere?', 'options' => json_encode(['Oxygen', 'Nitrogen', 'Carbon Dioxide', 'Hydrogen']), 'correct_answer' => 'Nitrogen', 'difficulty' => 'easy'],
            ['subject' => 'Chemistry', 'question' => 'What is the chemical formula for table salt?', 'options' => json_encode(['NaCl', 'KCl', 'CaCl2', 'MgCl2']), 'correct_answer' => 'NaCl', 'difficulty' => 'easy'],
            ['subject' => 'Chemistry', 'question' => 'What is an acid?', 'options' => json_encode(['pH > 7', 'pH < 7', 'pH = 7', 'pH = 0']), 'correct_answer' => 'pH < 7', 'difficulty' => 'medium'],
            ['subject' => 'Chemistry', 'question' => 'What is the valency of Oxygen?', 'options' => json_encode(['1', '2', '3', '4']), 'correct_answer' => '2', 'difficulty' => 'easy'],
            ['subject' => 'Chemistry', 'question' => 'What is the process of converting solid to gas called?', 'options' => json_encode(['Melting', 'Evaporation', 'Sublimation', 'Condensation']), 'correct_answer' => 'Sublimation', 'difficulty' => 'medium'],
            ['subject' => 'Chemistry', 'question' => 'What is the chemical symbol for Gold?', 'options' => json_encode(['Go', 'Gd', 'Au', 'Ag']), 'correct_answer' => 'Au', 'difficulty' => 'medium'],
            ['subject' => 'Chemistry', 'question' => 'What is the molecular formula of glucose?', 'options' => json_encode(['C6H12O6', 'C12H22O11', 'CH4', 'C2H5OH']), 'correct_answer' => 'C6H12O6', 'difficulty' => 'medium'],
            
            // English Questions (10 questions)
            ['subject' => 'English', 'question' => 'Which is the correct past tense of "go"?', 'options' => json_encode(['goed', 'went', 'gone', 'going']), 'correct_answer' => 'went', 'difficulty' => 'easy'],
            ['subject' => 'English', 'question' => 'What is a synonym for "happy"?', 'options' => json_encode(['sad', 'joyful', 'angry', 'tired']), 'correct_answer' => 'joyful', 'difficulty' => 'easy'],
            ['subject' => 'English', 'question' => 'Identify the noun in: "The cat runs quickly"', 'options' => json_encode(['The', 'cat', 'runs', 'quickly']), 'correct_answer' => 'cat', 'difficulty' => 'medium'],
            ['subject' => 'English', 'question' => 'What is an antonym of "hot"?', 'options' => json_encode(['warm', 'cold', 'cool', 'freezing']), 'correct_answer' => 'cold', 'difficulty' => 'easy'],
            ['subject' => 'English', 'question' => 'Which is a pronoun?', 'options' => json_encode(['run', 'he', 'quickly', 'table']), 'correct_answer' => 'he', 'difficulty' => 'easy'],
            ['subject' => 'English', 'question' => 'What is the plural of "child"?', 'options' => json_encode(['childs', 'children', 'childes', 'child']), 'correct_answer' => 'children', 'difficulty' => 'easy'],
            ['subject' => 'English', 'question' => 'Identify the verb: "She sings beautifully"', 'options' => json_encode(['She', 'sings', 'beautifully', 'none']), 'correct_answer' => 'sings', 'difficulty' => 'easy'],
            ['subject' => 'English', 'question' => 'What is an adjective?', 'options' => json_encode(['Action word', 'Describing word', 'Naming word', 'Connecting word']), 'correct_answer' => 'Describing word', 'difficulty' => 'medium'],
            ['subject' => 'English', 'question' => 'Which sentence is correct?', 'options' => json_encode(['He go to school', 'He goes to school', 'He going to school', 'He gone to school']), 'correct_answer' => 'He goes to school', 'difficulty' => 'medium'],
            ['subject' => 'English', 'question' => 'What is the superlative form of "good"?', 'options' => json_encode(['gooder', 'goodest', 'better', 'best']), 'correct_answer' => 'best', 'difficulty' => 'medium'],
            
            // Personality/Teaching Questions (10 questions)
            ['subject' => 'Personality', 'question' => 'A student comes to you feeling demotivated after failing a test. What\'s your approach?', 'options' => json_encode(['Tell them to work harder', 'Analyze mistakes together and create improvement plan', 'Compare with other students', 'Suggest they might not be good at subject']), 'correct_answer' => 'Analyze mistakes together and create improvement plan', 'difficulty' => 'medium'],
            ['subject' => 'Personality', 'question' => 'During a lesson, you notice the student is not understanding. What do you do?', 'options' => json_encode(['Move to next topic', 'Explain again same way', 'Use different approach with examples', 'Ask them to study alone']), 'correct_answer' => 'Use different approach with examples', 'difficulty' => 'medium'],
            ['subject' => 'Personality', 'question' => 'How would you explain a complex topic to a slow learner?', 'options' => json_encode(['Use simplified language and break into parts', 'Teach at same pace', 'Give extra homework', 'Ask to watch videos']), 'correct_answer' => 'Use simplified language and break into parts', 'difficulty' => 'medium'],
            ['subject' => 'Personality', 'question' => 'A parent complains their child is not improving. How do you respond?', 'options' => json_encode(['Blame the student', 'Share progress reports and discuss strategies', 'Ignore complaint', 'Suggest changing tutor']), 'correct_answer' => 'Share progress reports and discuss strategies', 'difficulty' => 'medium'],
            ['subject' => 'Personality', 'question' => 'What is your teaching philosophy?', 'options' => json_encode(['Focus only on syllabus completion', 'Adapt to each student\'s learning style', 'Strict discipline only', 'Homework is most important']), 'correct_answer' => 'Adapt to each student\'s learning style', 'difficulty' => 'medium'],
            ['subject' => 'Personality', 'question' => 'How do you handle a disruptive student?', 'options' => json_encode(['Punish immediately', 'Understand reason and address calmly', 'Ignore behavior', 'Complain to parents']), 'correct_answer' => 'Understand reason and address calmly', 'difficulty' => 'medium'],
            ['subject' => 'Personality', 'question' => 'A student asks a question you don\'t know. What do you do?', 'options' => json_encode(['Pretend to know', 'Admit and promise to find answer', 'Change topic', 'Scold for asking']), 'correct_answer' => 'Admit and promise to find answer', 'difficulty' => 'medium'],
            ['subject' => 'Personality', 'question' => 'How do you make learning interesting?', 'options' => json_encode(['Only textbook teaching', 'Use real-life examples and activities', 'Give more tests', 'Strict rules']), 'correct_answer' => 'Use real-life examples and activities', 'difficulty' => 'medium'],
            ['subject' => 'Personality', 'question' => 'What do you do if a student is consistently late?', 'options' => json_encode(['Ban from class', 'Understand reason and find solution', 'Ignore it', 'Reduce their marks']), 'correct_answer' => 'Understand reason and find solution', 'difficulty' => 'medium'],
            ['subject' => 'Personality', 'question' => 'How do you measure student progress?', 'options' => json_encode(['Only final exam marks', 'Regular assessments and feedback', 'Compare with others', 'Homework completion only']), 'correct_answer' => 'Regular assessments and feedback', 'difficulty' => 'medium'],
        ];

        foreach ($questions as $question) {
            \DB::table('test_questions')->insert(array_merge($question, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
