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
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('pet_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamp('applied_at')->useCurrent();
            
            // Personal Information
            $table->string('full_name');
            $table->string('email');
            $table->string('phone_number');
            $table->integer('age')->nullable();
            
            // Living Situation
            $table->string('residence_type');
            $table->string('own_or_rent');
            $table->string('has_yard');
            
            // Pet Experience
            $table->string('owned_pets_before');
            $table->string('has_other_pets');
            $table->text('other_pets_details')->nullable();
            
            // Commitment & Care
            $table->string('hours_alone');
            $table->text('adoption_reason');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
