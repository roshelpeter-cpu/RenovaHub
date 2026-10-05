<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Demonstration records for the homeowner workspace.
 * Passwords are hashed in the seeders and are never rendered in a view.
 */
class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            HomeownerDemoSeeder::class,
            ProfessionalDemoSeeder::class,
            ExploreProfessionalSeeder::class,
            ProjectDemoSeeder::class,
            QuotationDemoSeeder::class,
            DocumentDemoSeeder::class,
            MoodBoardDemoSeeder::class,
            ChangeRequestDemoSeeder::class,
            MessageDemoSeeder::class,
            ConversationDemoSeeder::class,
            PaymentDemoSeeder::class,
            ProjectFeedbackDemoSeeder::class,
            NotificationDemoSeeder::class,
            ActivityDemoSeeder::class,
            DesignerWorkspaceSeeder::class,
            ContractorWorkspaceSeeder::class,
        ]);
    }
}
