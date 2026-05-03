<?php

namespace Database\Seeders;

use App\Crm\Models\Activity;
use App\Crm\Models\Company;
use App\Crm\Models\Contact;
use App\Crm\Models\Deal;
use App\Crm\Models\EmailMessage;
use App\Crm\Models\Invoice;
use App\Crm\Models\Lead;
use App\Crm\Models\Pipeline;
use App\Crm\Models\PipelineStage;
use App\Crm\Models\Quote;
use App\Crm\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class CrmDemoSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->updateOrCreate(
            ['email' => 'crm.admin@example.com'],
            [
                'name' => 'CRM Admin',
                'username' => 'crmadmin',
                'password' => 'password123',
                'crm_role' => 'admin',
                'crm_theme' => 'corporate',
            ]
        );

        $manager = User::query()->updateOrCreate(
            ['email' => 'crm.manager@example.com'],
            [
                'name' => 'CRM Manager',
                'username' => 'crmmanager',
                'password' => 'password123',
                'crm_role' => 'manager',
                'crm_theme' => 'dark',
            ]
        );

        $sales = User::query()->updateOrCreate(
            ['email' => 'crm.sales@example.com'],
            [
                'name' => 'CRM Sales Rep',
                'username' => 'crmsales',
                'password' => 'password123',
                'crm_role' => 'sales_rep',
                'crm_theme' => 'minimal',
            ]
        );

        $pipeline = Pipeline::query()->updateOrCreate(
            ['name' => 'Primary Sales Pipeline'],
            ['is_default' => true]
        );

        $stages = collect([
            ['name' => 'Qualification', 'position' => 1, 'probability' => 20, 'color' => '#1b6fd8'],
            ['name' => 'Discovery', 'position' => 2, 'probability' => 40, 'color' => '#0d9488'],
            ['name' => 'Proposal', 'position' => 3, 'probability' => 70, 'color' => '#d0890e'],
            ['name' => 'Negotiation', 'position' => 4, 'probability' => 85, 'color' => '#7c3aed'],
            ['name' => 'Closed Won', 'position' => 5, 'probability' => 100, 'color' => '#1f9d63'],
        ])->map(function (array $stage) use ($pipeline) {
            return PipelineStage::query()->updateOrCreate(
                ['pipeline_id' => $pipeline->id, 'name' => $stage['name']],
                $stage
            );
        });

        $companyA = Company::query()->updateOrCreate(
            ['name' => 'Northwind Ventures'],
            [
                'industry' => 'Fintech',
                'website' => 'https://northwind.example.test',
                'email' => 'hello@northwind.test',
                'phone' => '+91 98765 11111',
                'address' => 'Mumbai, Maharashtra',
                'owner_id' => $manager->id,
                'notes' => 'Strategic expansion client.',
            ]
        );

        $companyB = Company::query()->updateOrCreate(
            ['name' => 'BluePeak Properties'],
            [
                'industry' => 'Real Estate',
                'website' => 'https://bluepeak.example.test',
                'email' => 'contact@bluepeak.test',
                'phone' => '+91 98765 22222',
                'address' => 'Bengaluru, Karnataka',
                'owner_id' => $sales->id,
                'notes' => 'Fast-moving commercial portfolio.',
            ]
        );

        $contactA = Contact::query()->updateOrCreate(
            ['email' => 'anika@northwind.test'],
            [
                'company_id' => $companyA->id,
                'first_name' => 'Anika',
                'last_name' => 'Sharma',
                'phone' => '+91 90000 11111',
                'title' => 'VP Operations',
                'status' => 'active',
                'owner_id' => $manager->id,
                'last_contacted_at' => now()->subDay(),
                'notes' => 'Prefers weekly updates.',
            ]
        );

        $contactB = Contact::query()->updateOrCreate(
            ['email' => 'karan@bluepeak.test'],
            [
                'company_id' => $companyB->id,
                'first_name' => 'Karan',
                'last_name' => 'Malhotra',
                'phone' => '+91 90000 22222',
                'title' => 'Commercial Director',
                'status' => 'active',
                'owner_id' => $sales->id,
                'last_contacted_at' => now()->subDays(2),
                'notes' => 'Ready for pricing discussions.',
            ]
        );

        $dealA = Deal::query()->updateOrCreate(
            ['name' => 'Northwind Automation Rollout'],
            [
                'company_id' => $companyA->id,
                'contact_id' => $contactA->id,
                'pipeline_id' => $pipeline->id,
                'stage_id' => $stages->firstWhere('name', 'Proposal')->id,
                'value' => 850000,
                'status' => 'open',
                'expected_close_at' => now()->addWeeks(3),
                'owner_id' => $manager->id,
                'notes' => 'Proposal shared, awaiting commercial review.',
            ]
        );

        $dealB = Deal::query()->updateOrCreate(
            ['name' => 'BluePeak Portfolio Enablement'],
            [
                'company_id' => $companyB->id,
                'contact_id' => $contactB->id,
                'pipeline_id' => $pipeline->id,
                'stage_id' => $stages->firstWhere('name', 'Negotiation')->id,
                'value' => 1425000,
                'status' => 'open',
                'expected_close_at' => now()->addWeeks(2),
                'owner_id' => $sales->id,
                'notes' => 'Legal and pricing under review.',
            ]
        );

        Lead::query()->updateOrCreate(
            ['title' => 'Inbound Fintech Expansion'],
            [
                'company_id' => $companyA->id,
                'contact_id' => $contactA->id,
                'title' => 'Inbound Fintech Expansion',
                'source' => 'Website Form',
                'value' => 620000,
                'status' => 'qualified',
                'priority' => 'high',
                'owner_id' => $manager->id,
                'notes' => 'Lead routed from marketing automation landing page.',
                'converted_deal_id' => $dealA->id,
            ]
        );

        Lead::query()->updateOrCreate(
            ['title' => 'Commercial Sales Acceleration'],
            [
                'company_id' => $companyB->id,
                'contact_id' => $contactB->id,
                'title' => 'Commercial Sales Acceleration',
                'source' => 'Referral',
                'value' => 450000,
                'status' => 'proposal',
                'priority' => 'medium',
                'owner_id' => $sales->id,
                'notes' => 'Referral from existing client relationship.',
                'converted_deal_id' => $dealB->id,
            ]
        );

        Task::query()->updateOrCreate(
            ['title' => 'Prepare final quote package'],
            [
                'description' => 'Bundle pricing, implementation notes, and rollout timeline.',
                'status' => 'open',
                'priority' => 'high',
                'due_at' => now()->addDays(3),
                'assigned_to' => $sales->id,
                'created_by' => $manager->id,
            ]
        );

        Task::query()->updateOrCreate(
            ['title' => 'Client onboarding handoff'],
            [
                'description' => 'Document required implementation dependencies before kickoff.',
                'status' => 'in_progress',
                'priority' => 'medium',
                'due_at' => now()->addDays(6),
                'assigned_to' => $manager->id,
                'created_by' => $admin->id,
            ]
        );

        Activity::query()->updateOrCreate(
            ['activity_type' => 'deal_update', 'description' => 'Proposal reviewed with Northwind stakeholders.'],
            [
                'performed_by' => $manager->id,
                'occurred_at' => now()->subDay(),
                'meta' => ['deal_id' => $dealA->id],
            ]
        );

        Activity::query()->updateOrCreate(
            ['activity_type' => 'task_created', 'description' => 'Negotiation checklist assigned to sales rep.'],
            [
                'performed_by' => $admin->id,
                'occurred_at' => now()->subHours(8),
                'meta' => ['deal_id' => $dealB->id],
            ]
        );

        EmailMessage::query()->updateOrCreate(
            ['subject' => 'Proposal follow-up and timeline'],
            [
                'company_id' => $companyA->id,
                'contact_id' => $contactA->id,
                'deal_id' => $dealA->id,
                'direction' => 'outbound',
                'status' => 'sent',
                'body' => 'Sharing the updated commercial proposal and next-step timeline.',
                'sent_by' => $manager->id,
                'sent_at' => now()->subHours(20),
            ]
        );

        $quote = Quote::query()->updateOrCreate(
            ['quote_number' => 'QT-CRM-1001'],
            [
                'company_id' => $companyA->id,
                'contact_id' => $contactA->id,
                'deal_id' => $dealA->id,
                'status' => 'sent',
                'issue_date' => now()->subDays(2),
                'valid_until' => now()->addDays(15),
                'subtotal' => 720000,
                'tax' => 129600,
                'total' => 849600,
                'notes' => 'Implementation split into two milestones.',
                'created_by' => $manager->id,
            ]
        );

        $quote->items()->delete();
        $quote->items()->createMany([
            ['description' => 'CRM implementation', 'quantity' => 1, 'unit_price' => 500000, 'total' => 500000],
            ['description' => 'Training and enablement', 'quantity' => 1, 'unit_price' => 220000, 'total' => 220000],
        ]);

        $invoice = Invoice::query()->updateOrCreate(
            ['invoice_number' => 'INV-CRM-2001'],
            [
                'company_id' => $companyB->id,
                'contact_id' => $contactB->id,
                'deal_id' => $dealB->id,
                'status' => 'draft',
                'issue_date' => now(),
                'due_date' => now()->addDays(30),
                'subtotal' => 980000,
                'tax' => 176400,
                'total' => 1156400,
                'notes' => 'Advance billing for phase one rollout.',
                'created_by' => $admin->id,
            ]
        );

        $invoice->items()->delete();
        $invoice->items()->createMany([
            ['description' => 'Platform rollout phase one', 'quantity' => 1, 'unit_price' => 780000, 'total' => 780000],
            ['description' => 'Migration workshop', 'quantity' => 1, 'unit_price' => 200000, 'total' => 200000],
        ]);
    }
}
