<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Aligns the public-facing content tables with the columns the controllers
 * select. Production was missing columns (notably created_at / updated_at),
 * which made the explicit-column SELECTs fail and the pages fall back to
 * their empty state. Every change is guarded by hasTable/hasColumn so this
 * migration is safe to run regardless of the current production schema.
 */
return new class extends Migration
{
    public function up(): void
    {
        // customer_stories — client reviews
        $this->ensure('customer_stories', function (Blueprint $table) {
            $this->stringIfMissing($table, 'customer_stories', 'name');
            $this->stringIfMissing($table, 'customer_stories', 'position');
            if (!Schema::hasColumn('customer_stories', 'body_content')) {
                $table->text('body_content')->nullable();
            }
            $this->stringIfMissing($table, 'customer_stories', 'profile');
            $this->timestampsIfMissing($table, 'customer_stories');
        });

        // industry_listings — case studies
        $this->ensure('industry_listings', function (Blueprint $table) {
            $this->stringIfMissing($table, 'industry_listings', 'category');
            $this->stringIfMissing($table, 'industry_listings', 'listing_image');
            if (!Schema::hasColumn('industry_listings', 'body')) {
                $table->text('body')->nullable();
            }
            $this->stringIfMissing($table, 'industry_listings', 'pdf_url');
            $this->timestampsIfMissing($table, 'industry_listings');
        });

        // company_portfolios — innovation brands
        $this->ensure('company_portfolios', function (Blueprint $table) {
            $this->stringIfMissing($table, 'company_portfolios', 'title', false, '');
            $this->stringIfMissing($table, 'company_portfolios', 'category');
            if (!Schema::hasColumn('company_portfolios', 'short_description')) {
                $table->text('short_description')->default('');
            }
            if (!Schema::hasColumn('company_portfolios', 'long_description')) {
                $table->text('long_description')->nullable();
            }
            if (!Schema::hasColumn('company_portfolios', 'features')) {
                $table->longText('features')->nullable();
            }
            $this->stringIfMissing($table, 'company_portfolios', 'logo_path');
            $this->stringIfMissing($table, 'company_portfolios', 'cta_label');
            $this->stringIfMissing($table, 'company_portfolios', 'cta_url');
            if (!Schema::hasColumn('company_portfolios', 'display_order')) {
                $table->unsignedInteger('display_order')->default(0);
            }
            if (!Schema::hasColumn('company_portfolios', 'is_active')) {
                $table->boolean('is_active')->default(1);
            }
            $this->timestampsIfMissing($table, 'company_portfolios');
        });

        // core_values
        $this->ensure('core_values', function (Blueprint $table) {
            $this->stringIfMissing($table, 'core_values', 'title');
            if (!Schema::hasColumn('core_values', 'body')) {
                $table->text('body')->nullable();
            }
            $this->stringIfMissing($table, 'core_values', 'icon_font');
            $this->timestampsIfMissing($table, 'core_values');
        });
    }

    /**
     * Intentionally non-destructive: we do not drop columns on rollback,
     * because they may have pre-existed before this gap-fill migration ran.
     */
    public function down(): void
    {
        // no-op
    }

    private function ensure(string $tableName, \Closure $callback): void
    {
        if (!Schema::hasTable($tableName)) {
            return;
        }

        Schema::table($tableName, $callback);
    }

    private function stringIfMissing(Blueprint $table, string $tableName, string $column, bool $nullable = true, ?string $default = null): void
    {
        if (Schema::hasColumn($tableName, $column)) {
            return;
        }

        $definition = $table->string($column);

        if ($nullable) {
            $definition->nullable();
        }

        if ($default !== null) {
            $definition->default($default);
        }
    }

    private function timestampsIfMissing(Blueprint $table, string $tableName): void
    {
        if (!Schema::hasColumn($tableName, 'created_at')) {
            $table->timestamp('created_at')->nullable();
        }

        if (!Schema::hasColumn($tableName, 'updated_at')) {
            $table->timestamp('updated_at')->nullable();
        }
    }
};
