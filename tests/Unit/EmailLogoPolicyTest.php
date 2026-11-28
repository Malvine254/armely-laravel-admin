<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class EmailLogoPolicyTest extends TestCase
{
    public function test_email_wordmark_can_be_embedded_without_relaxing_the_default_resource_policy(): void
    {
        $policy = file_get_contents(__DIR__.'/../../public/.htaccess');

        $this->assertStringContainsString('Header always set Cross-Origin-Resource-Policy "same-site"', $policy);
        // Only the two email wordmarks are allowed to load cross-origin.
        $this->assertStringContainsString(
            '<FilesMatch "^(logo-replace-v2|armely-logo-email)\.png$">' . "\n"
            . '        Header always set Cross-Origin-Resource-Policy "cross-origin"' . "\n"
            . '    </FilesMatch>',
            str_replace("\r\n", "\n", $policy)
        );
        // Exactly two exceptions: the email wordmarks and the store image proxy
        // (product photos in marketing emails). Everything else stays same-site.
        $this->assertSame(2, substr_count($policy, 'Cross-Origin-Resource-Policy "cross-origin"'));
        $this->assertStringContainsString('<If "%{THE_REQUEST} =~ m#^(GET|HEAD)\s+/store/api/v1/img-proxy[?\s]#">', $policy);
        $this->assertFileExists(__DIR__.'/../../public/images/logo/logo-replace-v2.png');
        $this->assertFileExists(__DIR__.'/../../public/images/logo/armely-logo-email.png');
    }
}
