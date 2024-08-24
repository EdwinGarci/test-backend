<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('voucher_lines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->integer('quantity');
            $table->decimal('unit_price', 8, 2);
            $table->uuid('voucher_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('voucher_id')
                ->references('id')
                ->on('vouchers')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voucher_lines');
    }
};
