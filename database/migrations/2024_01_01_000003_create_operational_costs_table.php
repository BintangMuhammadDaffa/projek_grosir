<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOperationalCostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('operational_costs', function (Blueprint $table) {
            $table->id();
            $table->string('cost_code')->unique();
            $table->string('description');
            $table->enum('category', ['rent', 'utilities', 'salary', 'marketing', 'maintenance', 'other']);
            $table->decimal('amount', 15, 2);
            $table->date('cost_date');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('operational_costs');
    }
}
