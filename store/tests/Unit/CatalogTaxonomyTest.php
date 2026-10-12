<?php

namespace Tests\Unit;

use App\Support\CatalogTaxonomy;
use PHPUnit\Framework\TestCase;

class CatalogTaxonomyTest extends TestCase
{
    public function test_representative_images_reject_consumables_and_ambiguous_all_in_one_products(): void
    {
        $include = ['desktop', 'workstation', 'thinkcentre', 'all-in-one'];
        $exclude = ['printer', 'scanner', 'multifunction', 'keyboard', 'stand'];
        foreach ([
            'Lexmark all-in-one toner cartridge',
            'HP all-in-one printer',
            'Desktop replacement memory',
            'Desktop support license',
            'Desktop stand',
            'All-in-one ribbon supplies',
            'Desktop keyboard',
        ] as $name) {
            $this->assertFalse(CatalogTaxonomy::isRepresentativeProduct($name, $include, $exclude), $name);
        }
        $this->assertTrue(CatalogTaxonomy::isRepresentativeProduct('Lenovo ThinkCentre all-in-one PC', $include, $exclude));
        $this->assertTrue(CatalogTaxonomy::isRepresentativeProduct('HP desktop workstation', $include, $exclude));
    }

    public function test_representative_matching_does_not_confuse_word_fragments_with_accessories(): void
    {
        $this->assertTrue(CatalogTaxonomy::isRepresentativeProduct('Samsung monitor', ['monitor'], ['arm']));
        $this->assertFalse(CatalogTaxonomy::isRepresentativeProduct('Monitor arm', ['monitor'], ['arm']));
        $this->assertFalse(CatalogTaxonomy::isRepresentativeProduct('Monitoring software', ['monitor'], []));
        $this->assertTrue(CatalogTaxonomy::isRepresentativeProduct('Wireless access-point', ['access point'], []));
    }

    public function test_memory_terms_match_as_words_instead_of_substrings(): void
    {
        $this->assertSame('Memory & Storage Upgrades', CatalogTaxonomy::inferCategoryName('', 'Kingston 32GB DDR5 RAM memory module', ''));
        $this->assertNotSame('Memory & Storage Upgrades', CatalogTaxonomy::inferCategoryName('', 'Programmable conference camera', ''));
    }

    public function test_hardware_patterns_match_phrases_and_plurals_without_word_fragments(): void
    {
        $this->assertSame(1, preg_match('/'.CatalogTaxonomy::keywordPattern(['access point']).'/i', 'Wireless ACCESS-POINTS'));
        $this->assertSame(1, preg_match('/'.CatalogTaxonomy::keywordPattern(['case', 'cable']).'/i', 'Notebook cases'));
        $this->assertSame(0, preg_match('/'.CatalogTaxonomy::keywordPattern(['monitor', 'arm']).'/i', 'Samsung monitoring software'));
        $this->assertFalse(CatalogTaxonomy::isRepresentativeProduct('Printer toner cartridges', ['printer'], ['cartridge']));
        $this->assertFalse(CatalogTaxonomy::isRepresentativeProduct('Notebook cases', ['notebook'], ['case']));
        $this->assertNull(CatalogTaxonomy::hardwareCategoryRules('05'));
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
