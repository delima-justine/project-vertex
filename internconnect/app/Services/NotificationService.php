<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\JobPosting;

class NotificationService
{
    /**
     * Create notification for new job posting
     */
    public static function notifyNewJobPost(JobPosting $jobPosting)
    {
        // Get all interns
        $interns = User::where('user_role', 'Intern')->get();

        foreach ($interns as $intern) {
            Notification::create([
                'user_id' => $intern->user_id,
                'type' => 'New Job Posted',
                'message' => "New job posted: {$jobPosting->title} in {$jobPosting->department}",
                'action_url' => route('intern.job.search'),
                'send_date' => now(),
                'is_read' => false,
            ]);
        }
    }

    /**
     * Create notification for recommended job posts
     */
    public static function notifyRecommendedJobPost(JobPosting $jobPosting, User $intern)
    {
        Notification::create([
            'user_id' => $intern->user_id,
            'type' => 'Job Recommendation',
            'message' => "Job recommended for you: {$jobPosting->title} at {$jobPosting->department}",
            'action_url' => route('intern.job.search'),
            'send_date' => now(),
            'is_read' => false,
        ]);
    }

    /**
     * Create generic notification
     */
    public static function notify($userId, $type, $message)
    {
        Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'message' => $message,
            'send_date' => now(),
            'is_read' => false,
        ]);
    }

    /**
     * Get recommended jobs for an intern based on their profile
     */
    public static function getRecommendedJobs(User $intern, $limit = 5)
    {
        $userAbout = strtolower($intern->about ?? '');
        
        // Get job postings that match keywords in user's about section
        $keywords = array_filter(explode(' ', $userAbout), function($word) {
            return strlen($word) > 3;
        });

        $query = JobPosting::query();
        
        if (!empty($keywords)) {
            foreach ($keywords as $keyword) {
                $query->orWhere('title', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%")
                    ->orWhere('department', 'like', "%{$keyword}%");
            }
        }

        return $query->limit($limit)->get();
    }
}
