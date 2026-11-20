<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            $table->decimal('ad_spend', 12, 2);
            $table->unsignedInteger('leads');
            $table->unsignedInteger('customers');
            $table->decimal('customer_value', 12, 2);
            $table->decimal('gross_margin', 5, 2);  
            $table->decimal('close_rate', 5, 2);     
            $table->string('response_time');
            $table->boolean('has_follow_up');

            $table->decimal('cpl', 12, 2)->nullable();
            $table->decimal('cpa', 12, 2)->nullable();
            $table->decimal('gross_profit_per_customer', 12, 2);
            $table->decimal('break_even_cpl', 12, 2);
            $table->decimal('break_even_cpa', 12, 2);
            $table->decimal('revenue', 12, 2);
            $table->decimal('net_profit', 12, 2);
            $table->decimal('roas', 8, 2)->nullable();
            $table->unsignedTinyInteger('score');
            $table->string('score_label');
            $table->string('bottleneck');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};