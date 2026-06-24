<?php

use App\Enums\DeliveryChannel;
use App\Enums\Priority;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->string('priority')->default(Priority::default());
            $table->string('channel')->default(DeliveryChannel::default());
            $table->text('message');
            $table->string('idempotency_key')->unique();
            $table->string('request_hash');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('batches');
    }
};
