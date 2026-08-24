<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->string('pt_group')->nullable()->after('customer_id');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->string('contact_person')->nullable()->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('leads', fn (Blueprint $table) => $table->dropColumn('pt_group'));
        Schema::table('customers', fn (Blueprint $table) => $table->dropColumn('contact_person'));
    }
};
