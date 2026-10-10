<?php

use App\Models\Institution;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('institution_id')->nullable()->after('asal_instansi')->constrained('institutions')->nullOnDelete();
        });

        // Migrasikan data asal_instansi yang sudah ada ke tabel institutions (case-insensitive dedup).
        DB::table('users')
            ->whereNotNull('asal_instansi')
            ->where('asal_instansi', '!=', '')
            ->select('id', 'asal_instansi')
            ->orderBy('id')
            ->each(function ($user) {
                $institution = Institution::findOrCreateByName($user->asal_instansi);

                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['institution_id' => $institution->id]);
            });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('asal_instansi');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('asal_instansi')->nullable()->after('npm');
        });

        DB::table('users')
            ->whereNotNull('institution_id')
            ->select('users.id', 'institutions.nama')
            ->join('institutions', 'institutions.id', '=', 'users.institution_id')
            ->orderBy('users.id')
            ->each(function ($row) {
                DB::table('users')->where('id', $row->id)->update(['asal_instansi' => $row->nama]);
            });

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('institution_id');
        });
    }
};
