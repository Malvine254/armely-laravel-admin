<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class TestTDSynnexXmlApiAccess extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tdsynnex:test-xml-api 
                            {--username= : PartnerFirst username}
                            {--password= : PartnerFirst password}
                            {--sandbox : Use sandbox endpoint}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test TD SYNNEX XML API access with PartnerFirst credentials';

    /**
     * TD SYNNEX XML API endpoints
     */
    protected const PRODUCTION_URL = 'https://api.tdsynnex.com/xml';
    protected const SANDBOX_URL = 'https://api-sandbox.tdsynnex.com/xml';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->line('');
        $this->line('═══════════════════════════════════════════════════════════');
        $this->info('   TD SYNNEX XML API - Access Test');
        $this->line('═══════════════════════════════════════════════════════════');
        $this->line('');

        // Get credentials
        $username = $this->option('username') ?? $this->ask('PartnerFirst Username', 'malvine.owuor@armely.com');
        $password = $this->option('password') ?? $this->secret('PartnerFirst Password');

        if (empty($password)) {
            $this->error('Password cannot be empty');
            return self::FAILURE;
        }

        // Validate special characters
        $this->validatePasswordCharacters($password);

        // Select endpoint
        $useSandbox = $this->option('sandbox');
        $baseUrl = $useSandbox ? self::SANDBOX_URL : self::PRODUCTION_URL;

        $this->line("\n[1] Testing with " . ($useSandbox ? 'SANDBOX' : 'PRODUCTION') . " endpoint...");
        $sandboxResult = $this->testCredentials($username, $password, $baseUrl);

        // Test the other endpoint too if main one failed
        if (!$sandboxResult) {
            $altUrl = $useSandbox ? self::PRODUCTION_URL : self::SANDBOX_URL;
            $this->line("\n[2] Testing with " . ($useSandbox ? 'PRODUCTION' : 'SANDBOX') . " endpoint...");
            $altResult = $this->testCredentials($username, $password, $altUrl);
        } else {
            $altResult = true;
        }

        // Summary
        $this->printSummary($sandboxResult, $altResult || !$sandboxResult);

        return ($sandboxResult || $altResult) ? self::SUCCESS : self::FAILURE;
    }

    /**
     * Test credentials against XML API
     *
     * @param string $username
     * @param string $password
     * @param string $baseUrl
     * @return bool
     */
    protected function testCredentials($username, $password, $baseUrl)
    {
        try {
            $this->line("   Endpoint: <info>$baseUrl</info>");
            $this->line("   Username: <info>$username</info>");

            $client = new Client([
                'base_uri' => $baseUrl,
                'timeout' => 10,
                'verify' => true,
            ]);

            // Attempt authentication test
            $this->line('   Attempting authentication...');
            
            $response = $client->get('/test', [
                'auth' => [$username, $password],
                'headers' => [
                    'Content-Type' => 'application/xml',
                    'Accept' => 'application/xml',
                ]
            ]);

            $statusCode = $response->getStatusCode();

            if ($statusCode === 200 || $statusCode === 201) {
                $this->info("   ✅ Authentication SUCCESSFUL (Status: $statusCode)");
                return true;
            } elseif ($statusCode === 401) {
                $this->error('   ❌ Authentication FAILED - Invalid credentials (401)');
                return false;
            } elseif ($statusCode === 403) {
                $this->error('   ❌ Access DENIED - Credentials lack XML API permission (403)');
                return false;
            } else {
                $this->warn("   ⚠️  Unexpected status code: $statusCode");
                return false;
            }

        } catch (RequestException $e) {
            $response = $e->getResponse();
            
            if ($response) {
                $statusCode = $response->getStatusCode();
                
                if ($statusCode === 401) {
                    $this->error('   ❌ Authentication FAILED - Invalid credentials');
                } elseif ($statusCode === 403) {
                    $this->error('   ❌ Access DENIED - Permission issue');
                } elseif ($statusCode === 404) {
                    $this->warn('   ⚠️  Endpoint not found - Verify URL with TD SYNNEX');
                } else {
                    $this->error("   ❌ HTTP Error $statusCode");
                }
            } else {
                $this->error('   ❌ CONNECTION ERROR - Cannot reach endpoint');
                $this->line('      Verify endpoint: ' . $baseUrl);
                $this->line('      Contact: helpdeskUS@tdsynnex.com');
            }
            
            return false;

        } catch (\Exception $e) {
            $this->error('   ❌ ERROR: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Validate password special characters
     *
     * @param string $password
     */
    protected function validatePasswordCharacters($password)
    {
        $allowedSpecialChars = ['!', '@', '$'];
        $specialChars = preg_match_all('/[!@#$%^&*\-_\.=+\[\]{};:\'",<>?\\\|`~]/', $password, $matches);

        if ($specialChars) {
            $chars = array_unique($matches[0]);
            $invalid = array_diff($chars, $allowedSpecialChars);

            if (!empty($invalid)) {
                $this->warn("\n⚠️  PASSWORD WARNING");
                $this->warn("   Your password contains invalid special characters: " . implode(', ', $invalid));
                $this->warn("   XML API only allows: !, @, $");
                $this->warn("   Consider updating password before production use.\n");
            }
        }
    }

    /**
     * Print test summary
     *
     * @param bool $result1
     * @param bool $result2
     */
    protected function printSummary($result1, $result2)
    {
        $this->line('');
        $this->line('═══════════════════════════════════════════════════════════');
        $this->info('   TEST SUMMARY');
        $this->line('═══════════════════════════════════════════════════════════');

        $status1 = $result1 ? '✅ PASS' : '❌ FAIL';
        $status2 = $result2 ? '✅ PASS' : '❌ FAIL';

        $this->line("   Primary endpoint:   $status1");
        $this->line("   Alternate endpoint: $status2");

        if (!($result1 || $result2)) {
            $this->error('');
            $this->error('   ⚠️  No endpoints accessible. Next steps:');
            $this->line('      1. Verify endpoint URLs with helpdeskUS@tdsynnex.com');
            $this->line('      2. Confirm account 677726-US enrollment is complete');
            $this->line('      3. Check password compliance (!, @, $ special chars only)');
        }

        $this->line('═══════════════════════════════════════════════════════════');
        $this->line('');
    }
}
