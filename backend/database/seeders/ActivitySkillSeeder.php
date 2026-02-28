<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ActivitySkill;

class ActivitySkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $skills = [
            // Music
            ['category' => 'Music', 'skill_name' => 'Guitar', 'sort_order' => 1],
            ['category' => 'Music', 'skill_name' => 'Piano', 'sort_order' => 2],
            ['category' => 'Music', 'skill_name' => 'Keyboard', 'sort_order' => 3],
            ['category' => 'Music', 'skill_name' => 'Drums', 'sort_order' => 4],
            ['category' => 'Music', 'skill_name' => 'Violin', 'sort_order' => 5],
            ['category' => 'Music', 'skill_name' => 'Flute', 'sort_order' => 6],
            ['category' => 'Music', 'skill_name' => 'Tabla', 'sort_order' => 7],
            ['category' => 'Music', 'skill_name' => 'Harmonium', 'sort_order' => 8],
            ['category' => 'Music', 'skill_name' => 'Singing (Classical)', 'sort_order' => 9],
            ['category' => 'Music', 'skill_name' => 'Singing (Western)', 'sort_order' => 10],
            ['category' => 'Music', 'skill_name' => 'Singing (Bollywood)', 'sort_order' => 11],
            
            // Dance
            ['category' => 'Dance', 'skill_name' => 'Classical Dance', 'sort_order' => 1],
            ['category' => 'Dance', 'skill_name' => 'Bharatanatyam', 'sort_order' => 2],
            ['category' => 'Dance', 'skill_name' => 'Kathak', 'sort_order' => 3],
            ['category' => 'Dance', 'skill_name' => 'Odissi', 'sort_order' => 4],
            ['category' => 'Dance', 'skill_name' => 'Kuchipudi', 'sort_order' => 5],
            ['category' => 'Dance', 'skill_name' => 'Contemporary Dance', 'sort_order' => 6],
            ['category' => 'Dance', 'skill_name' => 'Hip-Hop', 'sort_order' => 7],
            ['category' => 'Dance', 'skill_name' => 'Salsa', 'sort_order' => 8],
            ['category' => 'Dance', 'skill_name' => 'Bollywood Dance', 'sort_order' => 9],
            ['category' => 'Dance', 'skill_name' => 'Ballet', 'sort_order' => 10],
            ['category' => 'Dance', 'skill_name' => 'Freestyle', 'sort_order' => 11],
            
            // Fitness & Wellness
            ['category' => 'Fitness & Wellness', 'skill_name' => 'Yoga', 'sort_order' => 1],
            ['category' => 'Fitness & Wellness', 'skill_name' => 'Meditation', 'sort_order' => 2],
            ['category' => 'Fitness & Wellness', 'skill_name' => 'Zumba', 'sort_order' => 3],
            ['category' => 'Fitness & Wellness', 'skill_name' => 'Aerobics', 'sort_order' => 4],
            ['category' => 'Fitness & Wellness', 'skill_name' => 'Gym Training', 'sort_order' => 5],
            ['category' => 'Fitness & Wellness', 'skill_name' => 'Personal Training', 'sort_order' => 6],
            ['category' => 'Fitness & Wellness', 'skill_name' => 'Pilates', 'sort_order' => 7],
            ['category' => 'Fitness & Wellness', 'skill_name' => 'Martial Arts', 'sort_order' => 8],
            ['category' => 'Fitness & Wellness', 'skill_name' => 'Karate', 'sort_order' => 9],
            ['category' => 'Fitness & Wellness', 'skill_name' => 'Taekwondo', 'sort_order' => 10],
            
            // Arts & Crafts
            ['category' => 'Arts & Crafts', 'skill_name' => 'Painting', 'sort_order' => 1],
            ['category' => 'Arts & Crafts', 'skill_name' => 'Drawing', 'sort_order' => 2],
            ['category' => 'Arts & Crafts', 'skill_name' => 'Sketching', 'sort_order' => 3],
            ['category' => 'Arts & Crafts', 'skill_name' => 'Craft Work', 'sort_order' => 4],
            ['category' => 'Arts & Crafts', 'skill_name' => 'Pottery', 'sort_order' => 5],
            ['category' => 'Arts & Crafts', 'skill_name' => 'Sculpture', 'sort_order' => 6],
            ['category' => 'Arts & Crafts', 'skill_name' => 'Calligraphy', 'sort_order' => 7],
            ['category' => 'Arts & Crafts', 'skill_name' => 'Origami', 'sort_order' => 8],
            ['category' => 'Arts & Crafts', 'skill_name' => 'Rangoli', 'sort_order' => 9],
            
            // Sports
            ['category' => 'Sports', 'skill_name' => 'Cricket', 'sort_order' => 1],
            ['category' => 'Sports', 'skill_name' => 'Football', 'sort_order' => 2],
            ['category' => 'Sports', 'skill_name' => 'Tennis', 'sort_order' => 3],
            ['category' => 'Sports', 'skill_name' => 'Badminton', 'sort_order' => 4],
            ['category' => 'Sports', 'skill_name' => 'Table Tennis', 'sort_order' => 5],
            ['category' => 'Sports', 'skill_name' => 'Swimming', 'sort_order' => 6],
            ['category' => 'Sports', 'skill_name' => 'Basketball', 'sort_order' => 7],
            ['category' => 'Sports', 'skill_name' => 'Volleyball', 'sort_order' => 8],
            ['category' => 'Sports', 'skill_name' => 'Chess', 'sort_order' => 9],
            ['category' => 'Sports', 'skill_name' => 'Skating', 'sort_order' => 10],
            
            // Life Skills
            ['category' => 'Life Skills', 'skill_name' => 'Cooking', 'sort_order' => 1],
            ['category' => 'Life Skills', 'skill_name' => 'Baking', 'sort_order' => 2],
            ['category' => 'Life Skills', 'skill_name' => 'Photography', 'sort_order' => 3],
            ['category' => 'Life Skills', 'skill_name' => 'Videography', 'sort_order' => 4],
            ['category' => 'Life Skills', 'skill_name' => 'Video Editing', 'sort_order' => 5],
            ['category' => 'Life Skills', 'skill_name' => 'Public Speaking', 'sort_order' => 6],
            ['category' => 'Life Skills', 'skill_name' => 'Personality Development', 'sort_order' => 7],
            ['category' => 'Life Skills', 'skill_name' => 'Communication Skills', 'sort_order' => 8],
            ['category' => 'Life Skills', 'skill_name' => 'Gardening', 'sort_order' => 9],
            
            // Languages
            ['category' => 'Languages', 'skill_name' => 'English Speaking', 'sort_order' => 1],
            ['category' => 'Languages', 'skill_name' => 'French', 'sort_order' => 2],
            ['category' => 'Languages', 'skill_name' => 'Spanish', 'sort_order' => 3],
            ['category' => 'Languages', 'skill_name' => 'German', 'sort_order' => 4],
            ['category' => 'Languages', 'skill_name' => 'Japanese', 'sort_order' => 5],
            ['category' => 'Languages', 'skill_name' => 'Chinese (Mandarin)', 'sort_order' => 6],
            ['category' => 'Languages', 'skill_name' => 'Korean', 'sort_order' => 7],
            ['category' => 'Languages', 'skill_name' => 'Sanskrit', 'sort_order' => 8],
            
            // Technology
            ['category' => 'Technology', 'skill_name' => 'Coding for Kids', 'sort_order' => 1],
            ['category' => 'Technology', 'skill_name' => 'Robotics', 'sort_order' => 2],
            ['category' => 'Technology', 'skill_name' => 'Game Development', 'sort_order' => 3],
            ['category' => 'Technology', 'skill_name' => 'Web Design', 'sort_order' => 4],
            ['category' => 'Technology', 'skill_name' => 'Graphic Design', 'sort_order' => 5],
            ['category' => 'Technology', 'skill_name' => 'Animation', 'sort_order' => 6],
            ['category' => 'Technology', 'skill_name' => 'Digital Marketing', 'sort_order' => 7],
        ];

        foreach ($skills as $skill) {
            ActivitySkill::create($skill);
        }
    }
}
