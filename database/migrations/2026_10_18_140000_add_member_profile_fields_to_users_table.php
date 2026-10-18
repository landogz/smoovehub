<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->unique()->after('name');
            $table->string('gender')->nullable()->after('phone');
            $table->date('date_of_birth')->nullable()->after('gender');
            $table->json('member_settings')->nullable()->after('avatar_path');
        });

        $users = DB::table('users')->select('id', 'name', 'email', 'username')->get();

        foreach ($users as $user) {
            if (filled($user->username)) {
                continue;
            }

            $base = Str::slug((string) ($user->name ?: Str::before((string) $user->email, '@')), '_');
            $base = $base !== '' ? $base : 'member';
            $username = $base;
            $suffix = 1;

            while (DB::table('users')->where('username', $username)->where('id', '!=', $user->id)->exists()) {
                $username = $base.'_'.$suffix;
                $suffix++;
            }

            DB::table('users')->where('id', $user->id)->update(['username' => $username]);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['username']);
            $table->dropColumn(['username', 'gender', 'date_of_birth', 'member_settings']);
        });
    }
};
