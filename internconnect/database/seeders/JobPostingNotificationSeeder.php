<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\JobPosting;
use App\Models\User;
use App\Services\NotificationService;

class JobPostingNotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first HR user to be the job poster
        $hrUser = User::where('user_role', 'HR')->first();
        
        if (!$hrUser) {
            return;
        }

        $jobs = [
            [
                'title' => 'Digital Marketing Specialist',
                'department' => 'Marketing',
                'salary_range' => '₱20,000 - ₱30,000',
                'description' => 'We are seeking a talented Digital Marketing Specialist to join our dynamic marketing team. You will be responsible for developing and executing digital marketing strategies across multiple platforms including social media, email, and content marketing.',
                'requirements' => 'Bachelor\'s degree in Marketing or related field, 1+ years of digital marketing experience, proficiency in social media platforms',
            ],
            [
                'title' => 'Web Developer (PHP/Laravel)',
                'department' => 'IT',
                'salary_range' => '₱25,000 - ₱40,000',
                'description' => 'Join our IT team as a Web Developer specializing in PHP and Laravel. You will develop and maintain web applications, collaborate with the design team, and ensure code quality and best practices.',
                'requirements' => 'Proficiency in PHP and Laravel, MySQL database experience, understanding of RESTful APIs, Git version control',
            ],
            [
                'title' => 'Content Writer',
                'department' => 'Content',
                'salary_range' => '₱15,000 - ₱25,000',
                'description' => 'We\'re looking for a creative Content Writer to produce engaging and informative content for our blog, website, and social media channels. You\'ll work closely with the marketing and product teams.',
                'requirements' => 'Excellent writing skills, ability to research and write about various topics, familiarity with SEO best practices, portfolio of previous work',
            ],
            [
                'title' => 'UI/UX Designer',
                'department' => 'Design',
                'salary_range' => '₱22,000 - ₱35,000',
                'description' => 'Design user-friendly and visually appealing interfaces for our web and mobile applications. You\'ll conduct user research, create wireframes, and collaborate with developers to bring designs to life.',
                'requirements' => 'Proficiency in design tools like Figma or Adobe XD, understanding of UX principles, portfolio demonstrating design work, communication skills',
            ],
            [
                'title' => 'Business Analyst',
                'department' => 'Business',
                'salary_range' => '₱28,000 - ₱42,000',
                'description' => 'Help us improve business processes and drive digital transformation. You\'ll analyze business requirements, create documentation, and work with stakeholders to implement solutions.',
                'requirements' => 'Strong analytical skills, experience with business analysis, proficiency in data analysis tools, excellent communication abilities',
            ],
        ];

        foreach ($jobs as $jobData) {
            $jobData['posted_by_user_id'] = $hrUser->user_id;
            $jobData['post_date'] = now();

            $jobPosting = JobPosting::create($jobData);

            // Send notifications using the NotificationService
            NotificationService::notifyNewJobPost($jobPosting);
        }
    }
}
