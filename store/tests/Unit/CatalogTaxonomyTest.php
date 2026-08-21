<?php

namespace Tests\Unit;

use App\Support\CatalogTaxonomy;
use PHPUnit\Framework\TestCase;

class CatalogTaxonomyTest extends TestCase
{
    public function test_memory_terms_match_as_words_instead_of_substrings(): void
    {
        $this->assertSame('Memory & Storage Upgrades', CatalogTaxonomy::inferCategoryName('', 'Kingston 32GB DDR5 RAM memory module', ''));
        $this->assertNotSame('Memory & Storage Upgrades', CatalogTaxonomy::inferCategoryName('', 'Programmable conference camera', ''));
    }

    public function test_common_storage_abbreviations_are_classified_correctly(): void
    {
        $this->assertSame('Memory & Storage Upgrades', CatalogTaxonomy::inferCategoryName('', 'Samsung 2TB NVMe SSD', ''));
    }

    public function test_exact_curated_slug_remains_authoritative(): void
    {
        $this->assertSame('Memory & Storage Upgrades', CatalogTaxonomy::inferCategoryName('memory-storage-upgrades', 'Generic product', ''));
    }
}
