<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('DROP VIEW IF EXISTS all_event_registrations');

        DB::statement("
            CREATE VIEW all_event_registrations AS

            SELECT
                er.id                                                   AS id,
                'member'                                                AS participant_type,
                er.event_id,
                u.name                                                  AS display_name,
                u.email                                                 AS email,
                u.avatar                                                AS photo,
                NULL                                                    AS whatsapp,
                er.status,
                er.amount_paid,
                er.promo_code_used,
                er.discount_applied,
                er.payment_status,
                er.ticket_number,
                er.registered_at,
                er.created_at,
                er.updated_at
            FROM event_registrations er
            LEFT JOIN users u ON u.id = er.user_id

            UNION ALL

            SELECT
                gr.id + 100000000                                       AS id,
                'guest'                                                 AS participant_type,
                gr.event_id,
                CONCAT(gr.first_name, ' ', gr.last_name)               AS display_name,
                gr.email,
                gr.photo,
                gr.whatsapp,
                gr.status,
                gr.amount_paid,
                gr.promo_code_used,
                gr.discount_applied,
                gr.payment_status,
                gr.ticket_number,
                gr.registered_at,
                gr.created_at,
                gr.updated_at
            FROM guest_registrations gr
            WHERE gr.deleted_at IS NULL
        ");
    }

    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS all_event_registrations');
    }
};
