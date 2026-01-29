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
        Schema::table('applications', function (Blueprint $table) {
            // Personal Information
            $table->string('full_name')->after('applied_at');
            $table->string('email')->after('full_name');
            $table->string('phone_number')->after('email');
            $table->integer('age')->nullable()->after('phone_number');
            
            // Living Situation
            $table->string('residence_type')->after('age');
            $table->string('own_or_rent')->after('residence_type');
            $table->string('has_yard')->after('own_or_rent');
            
            // Pet Experience
            $table->string('owned_pets_before')->after('has_yard');
            $table->string('has_other_pets')->after('owned_pets_before');
            $table->text('other_pets_details')->nullable()->after('has_other_pets');
            
            // Commitment & Care
            $table->string('hours_alone')->after('other_pets_details');
            $table->text('adoption_reason')->after('hours_alone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn([
                'full_name',
                'email',
                'phone_number',
                'age',
                'residence_type',
                'own_or_rent',
                'has_yard',
                'owned_pets_before',
                'has_other_pets',
                'other_pets_details',
                'hours_alone',
                'adoption_reason',
            ]);
        });
    }
};
