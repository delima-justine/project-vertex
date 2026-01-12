<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Notification;
use App\Models\User;

class CreateSampleNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-sample-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create sample notifications for testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get the first intern
        $intern = User::where('user_role', 'Intern')->first();

        if (!$intern) {
            $this->error('No intern found');
            return;
        }

        // Create sample notifications
        $notifications = [
            [
                'type' => 'New Job Posted',
                'message' => 'New job posted: Senior Marketing Manager in Marketing',
            ],
            [
                'type' => 'Job Recommendation',
                'message' => 'Job recommended for you: Content Writer at Digital Agency',
            ],
            [
                'type' => 'Document Due',
                'message' => 'Your endorsement letter is due today',
            ],
            [
                'type' => 'New Job Posted',
                'message' => 'New job posted: Social Media Specialist in Marketing',
            ],
        ];

        foreach ($notifications as $notif) {
            Notification::create([
                'user_id' => $intern->user_id,
                'type' => $notif['type'],
                'message' => $notif['message'],
                'send_date' => now(),
                'is_read' => false,
            ]);
        }

        $this->info('Sample notifications created successfully for: ' . $intern->first_name . ' ' . $intern->last_name);
    }
}
