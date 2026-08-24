<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Constraint sisa enum lama menolak sumber lead baru (telpon, whatsapp, canvasing, dll)
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE leads DROP CONSTRAINT IF EXISTS leads_source_check');
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE leads ADD CONSTRAINT leads_source_check CHECK (source::text = ANY (ARRAY[('website'::character varying)::text, ('referral'::character varying)::text, ('cold_call'::character varying)::text, ('email'::character varying)::text, ('social_media'::character varying)::text, ('event'::character varying)::text, ('other'::character varying)::text]))");
        }
    }
};
