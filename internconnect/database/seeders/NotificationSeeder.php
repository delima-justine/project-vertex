<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Notification;
use App\Models\User;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first intern
        $intern = User::where('user_role', 'Intern')->first();

        if (!$intern) {
            return;
        }

        // Create sample notifications
        $notifications = [
            [
                'type' => 'New Job Posted',
                'message' => 'New job posted: Senior Marketing Manager in Marketing Department',
            ],
            [
                'type' => 'Job Recommendation',
                'message' => 'Job recommended for you: Content Writer at Digital Marketing Agency',
            ],
            [
                'type' => 'Document Due',
                'message' => 'Your endorsement letter from school is due on January 20, 2026',
            ],
            [
                'type' => 'New Job Posted',
                'message' => 'New job posted: Social Media Specialist in Marketing',
            ],
            [
                'type' => 'Milestone Achieved',
                'message' => 'Congratulations! You have completed 50% of your internship hours',
            ],
        ];

        foreach ($notifications as $notif) {
            Notification::create([
                'user_id' => $intern->user_id,
                'type' => $notif['type'],
                'message' => $notif['message'],
                'send_date' => now()->subHours(rand(1, 24)),
                'is_read' => false,
            ]);
        }
    }
}
