<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // We only create the extension if the driver is pgsql
        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement('CREATE EXTENSION IF NOT EXISTS vector');
        }

        Schema::create('countries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('code', 3)->unique();
            $table->string('currency', 3);
            $table->timestamps();
        });

        Schema::create('brands', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->index();
            $table->timestamps();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->uuid('parent_id')->nullable();
            $table->timestamps();
        });
        Schema::table('categories', function (Blueprint $table) {
            $table->foreign('parent_id')
                ->references('id')
                ->on('categories')
                ->onDelete('cascade');
        });

        Schema::create('stores', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('country_id')->constrained();
            $table->string('name');
            $table->string('url')->nullable();
            $table->string('thumbnail')->nullable();
            $table->enum('type', ['supermarket', 'pharmacy', 'technology', 'clothes', 'pets', 'library']);
            $table->timestamps();
        });


        Schema::create('store_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('store_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('category_id')->constrained()->cascadeOnDelete();
            $table->string('url');
            $table->boolean('is_active')->default(true);
            $table->index(['store_id', 'category_id']);
            $table->timestamps();
        });


        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('store_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('brand_id')->nullable()->constrained();
            $table->foreignUuid('category_id')->nullable()->constrained();
            $table->string('external_id')->index();
            $table->string('url')->unique();

            $table->string('title');
            $table->text('description')->nullable();

            $table->decimal('price', 12, 0)->index();
            $table->string('currency', 3)->default('CLP');

            $table->json('additional_data')->nullable();
            $table->timestamp('last_scraped_at')->useCurrent();
            $table->timestamps();
        });

        Schema::create('product_images', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_id')->constrained()->cascadeOnDelete();
            $table->string('image_url');
            $table->boolean('main')->default(false);
            $table->timestamps();
        });


        Schema::create('product_embeddings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });


        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE product_embeddings ADD COLUMN vector vector(768)');
            DB::statement('CREATE INDEX product_embedding_idx ON product_embeddings USING hnsw (vector vector_cosine_ops)');
        }


        Schema::create('price_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_id')->constrained()->cascadeOnDelete();
            $table->decimal('price', 12, 0);
            $table->timestamp('recorded_at');
            $table->index(['product_id', 'recorded_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_histories');
        Schema::dropIfExists('product_embeddings');
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('products');
        Schema::dropIfExists('store_categories');
        Schema::dropIfExists('stores');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('brands');
        Schema::dropIfExists('countries');
    }
};
