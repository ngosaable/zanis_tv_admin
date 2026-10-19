<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * - sliders.linked_type was an ENUM('movie','documentary','live','none') but the
     *   admin form sends 'live_channel' / 'series', which MySQL rejects.
     *   Convert it to a plain string column.
     * - users.role defaulted to 'admin', meaning anyone who registered became an admin.
     *   New users now default to 'subscriber'.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return; // MODIFY COLUMN is MySQL/MariaDB syntax (XAMPP uses MariaDB)
        }

        DB::statement("ALTER TABLE sliders MODIFY COLUMN linked_type VARCHAR(50) NOT NULL DEFAULT 'none'");
        DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(255) NOT NULL DEFAULT 'subscriber'");
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(255) NOT NULL DEFAULT 'admin'");
        // linked_type is left as VARCHAR on rollback to avoid truncating existing values.
    }
};
