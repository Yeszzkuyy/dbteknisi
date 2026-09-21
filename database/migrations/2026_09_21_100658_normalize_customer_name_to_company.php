<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ponytail: data-only fix, no down-restore (old combined name is derivable from contact_person + company)
        DB::table('customers')
            ->whereRaw("name = CONCAT(contact_person, '-', company)")
            ->update(['name' => DB::raw('company')]);
    }

    public function down(): void
    {
        // no-op: combined display is now derived in frontend, nothing to restore
    }
};
