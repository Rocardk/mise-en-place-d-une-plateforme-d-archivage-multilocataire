<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Wallo\FilamentCompanies\Socialite;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable(
                Socialite::hasSocialiteFeatures()
            );
            $table->rememberToken();
            $table->foreignId('current_company_id')->nullable();
            $table->foreignId('current_connected_account_id')->nullable();
            $table->string('profile_photo_path')->nullable();
            $table->date('askyourpdf_last_api_call')->nullable();
            $table->tinyInteger('askyourpdf_calls_number')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
