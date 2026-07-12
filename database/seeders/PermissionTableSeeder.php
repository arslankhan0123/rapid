<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Database\Seeder;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            [
                'name' => 'manage_customer_groups',
                'type' => 'Customers',
                'display_name' => 'Manage Customer Groups',
            ],
            [
                'name' => 'manage_customers',
                'type' => 'Customers',
                'display_name' => 'Manage Customers',
            ],
            [
                'name' => 'manage_staff_member',
                'type' => 'Members',
                'display_name' => 'Manage Staff Member',
            ],
            [
                'name' => 'manage_article_groups',
                'type' => 'Articles',
                'display_name' => 'Manage Article Groups',
            ],
            [
                'name' => 'manage_articles',
                'type' => 'Articles',
                'display_name' => 'Manage Articles',
            ],
            [
                'name' => 'manage_tags',
                'type' => 'Tags',
                'display_name' => 'Manage Tags',
            ],
            [
                'name' => 'manage_leads',
                'type' => 'Leads',
                'display_name' => 'Manage Leads',
            ],
            [
                'name' => 'manage_lead_status',
                'type' => 'Leads',
                'display_name' => 'Manage Lead Status',
            ],
            [
                'name' => 'manage_tasks',
                'type' => 'Tasks',
                'display_name' => 'Manage Tasks',
            ],
            [
                'name' => 'manage_ticket_priority',
                'type' => 'Tickets',
                'display_name' => 'Manage Ticket Priority',
            ],
            [
                'name' => 'manage_ticket_statuses',
                'type' => 'Tickets',
                'display_name' => 'Manage Ticket Statuses',
            ],
            [
                'name' => 'manage_tickets',
                'type' => 'Tickets',
                'display_name' => 'Manage Tickets',
            ],
            [
                'name' => 'manage_invoices',
                'type' => 'Invoices',
                'display_name' => 'Manage Invoices',
            ],
            [
                'name' => 'manage_payments',
                'type' => 'Payments',
                'display_name' => 'Manage Payments',
            ],
            [
                'name' => 'manage_payment_mode',
                'type' => 'Payments',
                'display_name' => 'Manage Payment Mode',
            ],
            [
                'name' => 'manage_credit_notes',
                'type' => 'Credit Note',
                'display_name' => 'Manage Credit Note',
            ],
            [
                'name' => 'manage_proposals',
                'type' => 'Proposals',
                'display_name' => 'Manage Proposals',
            ],
            [
                'name' => 'manage_estimates',
                'type' => 'Estimates',
                'display_name' => 'Manage Estimates',
            ],
            [
                'name' => 'manage_departments',
                'type' => 'Departments',
                'display_name' => 'Manage Departments',
            ],
            [
                'name' => 'manage_predefined_replies',
                'type' => 'Predefined Replies',
                'display_name' => 'Manage Predefined Replies',
            ],
            [
                'name' => 'manage_expense_category',
                'type' => 'Expenses',
                'display_name' => 'Manage Expense Category',
            ],
            [
                'name' => 'manage_expenses',
                'type' => 'Expenses',
                'display_name' => 'Manage Expenses',
            ],
            [
                'name' => 'manage_services',
                'type' => 'Services',
                'display_name' => 'Manage Services',
            ],
            [
                'name' => 'manage_items',
                'type' => 'Items',
                'display_name' => 'Manage Items',
            ],
            [
                'name' => 'manage_items_groups',
                'type' => 'Items',
                'display_name' => 'Manage Items Groups',
            ],
            [
                'name' => 'manage_tax_rates',
                'type' => 'TaxRate',
                'display_name' => 'Manage Tax Rates',
            ],
            [
                'name' => 'manage_announcements',
                'type' => 'Announcements',
                'display_name' => 'Manage Announcements',
            ],
            [
                'name' => 'manage_calenders',
                'type' => 'Calenders',
                'display_name' => 'Manage Calenders',
            ],
            [
                'name' => 'manage_lead_sources',
                'type' => 'Leads',
                'display_name' => 'Manage Lead Sources',
            ],
            [
                'name' => 'manage_contracts_types',
                'type' => 'Contracts',
                'display_name' => 'Manage Contract Types',
            ],
            [
                'name' => 'manage_contracts',
                'type' => 'Contracts',
                'display_name' => 'Manage Contracts',
            ],
            [
                'name' => 'manage_projects',
                'type' => 'Projects',
                'display_name' => 'Manage Projects',
            ],
            [
                'name' => 'manage_goals',
                'type' => 'Goals',
                'display_name' => 'Manage Goals',
            ],
            [
                'name' => 'manage_settings',
                'type' => 'Settings',
                'display_name' => 'Manage Settings',
            ],
            [
                'name' => 'contact_projects',
                'type' => 'Contacts',
                'display_name' => 'Contact Projects',
            ],
            [
                'name' => 'contact_invoices',
                'type' => 'Contacts',
                'display_name' => 'Contact Invoices',
            ],
            [
                'name' => 'contact_proposals',
                'type' => 'Contacts',
                'display_name' => 'Contact Proposals',
            ],
            [
                'name' => 'contact_contracts',
                'type' => 'Contacts',
                'display_name' => 'Contact Contracts',
            ],
            [
                'name' => 'contact_estimates',
                'type' => 'Contacts',
                'display_name' => 'Contact Estimates',
            ],
            // ===================================
            // 🔹 New Company Loans permissions
            // ===================================
            [
                'name' => 'view_company_loans',
                'type' => 'Company Loans',
                'display_name' => 'View',
            ],
            [
                'name' => 'create_company_loans',
                'type' => 'Company Loans',
                'display_name' => 'Create',
            ],
            [
                'name' => 'update_company_loans',
                'type' => 'Company Loans',
                'display_name' => 'Update',
            ],
            [
                'name' => 'delete_company_loans',
                'type' => 'Company Loans',
                'display_name' => 'Delete',
            ],
            [
                'name' => 'get_customers_by_type',
                'type' => 'Company Loans',
                'display_name' => 'Get Customers By Type',
            ],

            // ===================================
            // 🔹 New Documents permissions
            // ===================================
            [
                'name' => 'view_documents',
                'type' => 'Documents',
                'display_name' => 'View',
            ],
            [
                'name' => 'create_documents',
                'type' => 'Documents',
                'display_name' => 'Create',
            ],
            [
                'name' => 'update_documents',
                'type' => 'Documents',
                'display_name' => 'Update',
            ],
            [
                'name' => 'delete_documents',
                'type' => 'Documents',
                'display_name' => 'Delete',
            ],
            [
                'name' => 'download_documents',
                'type' => 'Documents',
                'display_name' => 'Download',
            ],

            // ===================================
            // 🔹 New Logged Users permissions
            // ===================================
            [
                'name' => 'view_logged_users',
                'type' => 'Logged Users',
                'display_name' => 'View Logged Users',
            ],
            [
                'name' => 'force_logout_logged_users',
                'type' => 'Logged Users',
                'display_name' => 'Force Logout Logged Users',
            ],

            // ===================================
            // 🔹 New Revoke permissions
            // ===================================
            [
                'name' => 'view_revokes',
                'type' => 'Revokes',
                'display_name' => 'View Revokes',
            ],
            [
                'name' => 'create_revokes',
                'type' => 'Revokes',
                'display_name' => 'Create Revokes',
            ],
            [
                'name' => 'update_revokes',
                'type' => 'Revokes',
                'display_name' => 'Update Revokes',
            ],
            [
                'name' => 'delete_revokes',
                'type' => 'Revokes',
                'display_name' => 'Delete Revokes',
            ],
            [
                'name' => 'view_safety_materials',
                'type' => 'Safety Materials',
                'display_name' => 'View Safety Materials',
            ],
            [
                'name' => 'create_safety_materials',
                'type' => 'Safety Materials',
                'display_name' => 'Create Safety Materials',
            ],
            [
                'name' => 'update_safety_materials',
                'type' => 'Safety Materials',
                'display_name' => 'Update Safety Materials',
            ],
            [
                'name' => 'delete_safety_materials',
                'type' => 'Safety Materials',
                'display_name' => 'Delete Safety Materials',
            ],
        ];

        $permissions = array_merge($permissions, [
            // ===================================
            // 🔹 New Master Accounts permissions
            // ===================================
            [
                'name' => 'view_master_accounts',
                'type' => 'Master Accounts',
                'display_name' => 'View Master Accounts',
            ],
            [
                'name' => 'create_master_accounts',
                'type' => 'Master Accounts',
                'display_name' => 'Create Master Accounts',
            ],
            [
                'name' => 'update_master_accounts',
                'type' => 'Master Accounts',
                'display_name' => 'Update Master Accounts',
            ],
            [
                'name' => 'delete_master_accounts',
                'type' => 'Master Accounts',
                'display_name' => 'Delete Master Accounts',
            ],
            [
                'name' => 'manage_job_categories',
                'type' => 'Job Categories',
                'display_name' => 'Manage Job Categories',
            ],
            [
                'name' => 'view_job_categories',
                'type' => 'Job Categories',
                'display_name' => 'View Job Categories',
            ],
            [
                'name' => 'create_job_categories',
                'type' => 'Job Categories',
                'display_name' => 'Create Job Categories',
            ],
            [
                'name' => 'update_job_categories',
                'type' => 'Job Categories',
                'display_name' => 'Update Job Categories',
            ],
            [
                'name' => 'delete_job_categories',
                'type' => 'Job Categories',
                'display_name' => 'Delete Job Categories',
            ],
            [
                'name' => 'manage_job_skills',
                'type' => 'Job Skills',
                'display_name' => 'Manage Job Skills',
            ],
            [
                'name' => 'view_job_skills',
                'type' => 'Job Skills',
                'display_name' => 'View Job Skills',
            ],
            [
                'name' => 'create_job_skills',
                'type' => 'Job Skills',
                'display_name' => 'Create Job Skills',
            ],
            [
                'name' => 'update_job_skills',
                'type' => 'Job Skills',
                'display_name' => 'Update Job Skills',
            ],
            [
                'name' => 'delete_job_skills',
                'type' => 'Job Skills',
                'display_name' => 'Delete Job Skills',
            ],
        ]);

        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        // Create all permissions (skip if already exists)
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission['name']], $permission);
        }

        // Assign new module permissions to Admin user
        $admin = User::find(1); // Assuming Admin user has ID 1
        if ($admin) {
            $masterPermissions = Permission::where('type', 'Master Accounts')->pluck('name')->toArray();
            $jobCategoriesPermissions = Permission::where('type', 'Job Categories')->pluck('name')->toArray();
            $jobSkillsPermissions = Permission::where('type', 'Job Skills')->pluck('name')->toArray();
            $admin->givePermissionTo(array_merge($masterPermissions, $jobCategoriesPermissions, $jobSkillsPermissions));
        }
    }
}
