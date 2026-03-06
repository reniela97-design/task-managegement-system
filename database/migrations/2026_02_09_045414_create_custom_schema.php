<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Drop existing tables to avoid conflicts (Reverse Order)
        Schema::dropIfExists('activity');
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('clients');
        Schema::dropIfExists('types');
        Schema::dropIfExists('status');
        Schema::dropIfExists('priorities');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('systems'); // inferred table
        Schema::dropIfExists('roles');
        Schema::dropIfExists('users');

        // 2. Create Tables (Without Foreign Keys first to avoid circular errors)

        // USERS
        Schema::create('users', function (Blueprint $table) {
            $table->id('user_id'); // Primary Key
            $table->string('user_name');
            $table->string('user_email')->unique();
            $table->string('user_password');
            $table->unsignedBigInteger('user_role_id')->nullable(); // FK added later
            $table->boolean('user_inactive')->default(false);
            $table->dateTime('user_log_datetime')->useCurrent();
        });

        // ROLES
        Schema::create('roles', function (Blueprint $table) {
            $table->id('role_id');
            $table->string('role_name');
            $table->unsignedBigInteger('role_user_id')->nullable(); // Who created this
            $table->dateTime('role_log_datetime')->useCurrent();
            $table->boolean('role_inactive')->default(false);
        });

        // CATEGORIES
        Schema::create('categories', function (Blueprint $table) {
            $table->id('category_id');
            $table->string('category_name');
            $table->unsignedBigInteger('category_user_id')->nullable();
            $table->dateTime('category_log_datetime')->useCurrent();
            $table->boolean('category_inactive')->default(false);
        });

        // SYSTEM (Inferred from context)
        Schema::create('systems', function (Blueprint $table) {
            $table->id('system_id');
            $table->string('system_name');
            $table->unsignedBigInteger('system_user_id')->nullable();
            $table->dateTime('system_log_datetime')->useCurrent();
            $table->boolean('system_inactive')->default(false);
        });

        // PRIORITIES
        Schema::create('priorities', function (Blueprint $table) {
            $table->id('priority_id'); // Corrected spelling for ID consistency
            $table->string('priority_name');
            $table->unsignedBigInteger('priority_user_id')->nullable();
            $table->dateTime('priority_log_datetime')->useCurrent();
            $table->boolean('priority_inactive')->default(false);
        });

        // STATUS
        Schema::create('status', function (Blueprint $table) { // Table name usually plural 'statuses' but using 'status' as requested
            $table->id('status_id');
            $table->string('status_name');
            $table->unsignedBigInteger('status_user_id')->nullable();
            $table->dateTime('status_log_datetime')->useCurrent();
            $table->string('status_color')->nullable();
            $table->boolean('status_inactive')->default(false);
        });

        // TYPES
        Schema::create('types', function (Blueprint $table) {
            $table->id('type_id');
            $table->string('type_name');
            $table->unsignedBigInteger('type_user_id')->nullable();
            $table->dateTime('type_log_datetime')->useCurrent();
            $table->boolean('type_inactive')->default(false);
        });

        // CLIENTS
        Schema::create('clients', function (Blueprint $table) {
            $table->id('client_id');
            $table->string('client_name');
            $table->string('client_contact_person')->nullable();
            $table->string('client_contact_number')->nullable();
            $table->unsignedBigInteger('client_user_id')->nullable();
            $table->dateTime('client_log_datetime')->useCurrent();
            $table->boolean('client_inactive')->default(false);
        });

        // PROJECTS
        Schema::create('projects', function (Blueprint $table) {
            $table->id('project_id');
            $table->string('project_name');
            $table->unsignedBigInteger('project_client_id');
            $table->string('project_branch')->nullable();
            $table->text('project_address')->nullable();
            $table->unsignedBigInteger('project_user_id')->nullable();
            $table->dateTime('project_log_datetime')->useCurrent();
            $table->boolean('project_inactive')->default(false);
        });

        // TASKS
        Schema::create('tasks', function (Blueprint $table) {
            $table->id('task_id');
            $table->string('task_title');
            $table->text('task_description')->nullable();
            
            // FK Columns
            $table->unsignedBigInteger('task_client_id')->nullable();
            $table->unsignedBigInteger('task_project_id')->nullable();
            $table->unsignedBigInteger('task_system_id')->nullable();
            $table->unsignedBigInteger('task_category_id')->nullable();
            $table->unsignedBigInteger('task_priority_id')->nullable();
            $table->unsignedBigInteger('task_status_id')->nullable();
            $table->unsignedBigInteger('task_type_id')->nullable();
            $table->unsignedBigInteger('task_assign_to')->nullable(); // Assignee
            $table->unsignedBigInteger('task_user_id')->nullable();   // Creator
            
            // Dates/Times
            $table->date('task_due_date')->nullable();
            $table->date('task_date_start')->nullable();
            $table->date('task_date_end')->nullable();
            $table->time('task_time_start')->nullable();
            $table->time('task_time_end')->nullable();
            
            $table->text('task_remarks')->nullable();
            $table->dateTime('task_log_datetime')->useCurrent();
            $table->boolean('task_inactive')->default(false);
        });

        // ACTIVITY
        Schema::create('activity', function (Blueprint $table) {
            $table->id('activity_id');
            $table->string('activity_description');
            $table->unsignedBigInteger('activity_user_id');
            $table->string('activity_ip_address')->nullable();
            $table->string('activity_agent')->nullable();
            $table->dateTime('activity_log_datetime')->useCurrent();
        });

        // 3. Add Foreign Keys Constraints (Now that all tables exist)
        
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('user_role_id')->references('role_id')->on('roles');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->foreign('project_client_id')->references('client_id')->on('clients')->onDelete('cascade');
            $table->foreign('project_user_id')->references('user_id')->on('users');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->foreign('task_client_id')->references('client_id')->on('clients');
            $table->foreign('task_project_id')->references('project_id')->on('projects');
            $table->foreign('task_system_id')->references('system_id')->on('systems');
            $table->foreign('task_category_id')->references('category_id')->on('categories');
            $table->foreign('task_priority_id')->references('priority_id')->on('priorities');
            $table->foreign('task_status_id')->references('status_id')->on('status');
            $table->foreign('task_type_id')->references('type_id')->on('types');
            $table->foreign('task_assign_to')->references('user_id')->on('users');
            $table->foreign('task_user_id')->references('user_id')->on('users');
        });
        
        // Add user log constraints to other tables
        $tables = ['roles', 'categories', 'systems', 'priorities', 'status', 'types', 'clients'];
        foreach($tables as $tbl) {
            $singular = rtrim($tbl, 's');
            if($tbl == 'status') $singular = 'status'; // fix pluralization issue
            if($tbl == 'categories') $singular = 'category';
            if($tbl == 'priorities') $singular = 'priority';

            Schema::table($tbl, function (Blueprint $table) use ($singular) {
                $table->foreign($singular.'_user_id')->references('user_id')->on('users');
            });
        }
    }

    public function down(): void
    {
        // Drop in reverse order of creation/dependency
        Schema::dropIfExists('activity');
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('clients');
        Schema::dropIfExists('types');
        Schema::dropIfExists('status');
        Schema::dropIfExists('priorities');
        Schema::dropIfExists('systems');
        Schema::dropIfExists('categories');
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['user_role_id']);
        });
        Schema::dropIfExists('roles');
        Schema::dropIfExists('users');
    }
};
