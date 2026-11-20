<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title', 120);
            $table->text('notes')->nullable();
            $table->boolean('done')->default(false);
            $table->unsignedTinyInteger('priority')->default(2); // 1 high, 2 normal, 3 low
            $table->date('due_on')->nullable();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->index(['done', 'due_on']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
