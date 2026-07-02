<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
		$table->id();
    		$table->foreignId('customer_id')
          		->constrained()
          		->cascadeOnDelete();
		// $table->foreignId('company_id')
          		// ->constrained()
          		// ->cascadeOnDelete();
    		$table->foreignId('account_manager_id')
          		->nullable()
          		->constrained()
          		->nullOnDelete();
    		$table->foreignId('work_type_id')
          		->constrained()
          		->cascadeOnDelete();
    		$table->foreignId('pic_engineer_id')
          		->constrained('users')
          		->cascadeOnDelete();
		$table->string('project_name');

		$table->string('project_code')->nullable()->unique();

		$table->string('quotation_number')->nullable();


    		$table->enum('status', [
        		'Open',
        		'On Progress',
        		'Done',
        		'Cancelled'
    		])->default('Open');
    		$table->date('start_date')->nullable();
    		$table->date('end_date')->nullable();
    		$table->text('description')->nullable();
    		$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
