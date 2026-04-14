<?php
declare(strict_types=1);

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use App\Jobs\DownloadProductImagesJob;
use App\Jobs\EnrichPriceAvailabilityDescriptionsJob;
use App\Jobs\EnrichPriceAvailabilityImagesJob;
use App\Models\Company;
use App\Models\Product;
use App\Models\User;

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$actions = [
    'status' => [
        'label' => 'Show Status',
        'category' => 'Diagnostics',
        'help' => 'View environment, storage, chat schema, and sync scope status.',
        'type' => 'status',
    ],
    'catalog_diagnostics' => [
        'label' => 'Catalog Diagnostics Report',
        'category' => 'Diagnostics',
        'help' => 'Show TD SYNNEX product counts, source flags, and current catalog rows.',
        'type' => 'catalog_diagnostics',
    ],
    'storage_link' => [
        'label' => 'Run storage:link',
        'category' => 'Storage & Cache',
        'help' => 'Recreate the public storage symlink.',
        'type' => 'artisan',
        'commands' => [
            ['name' => 'storage:link', 'params' => []],
        ],
    ],
    'optimize_clear' => [
        'label' => 'Run optimize:clear',
        'category' => 'Storage & Cache',
        'help' => 'Clear Laravel compiled bootstrap caches.',
        'type' => 'artisan',
        'commands' => [
            ['name' => 'optimize:clear', 'params' => []],
        ],
    ],
    'cache_clear' => [
        'label' => 'Run cache:clear',
        'category' => 'Storage & Cache',
        'help' => 'Clear the application cache store.',
        'type' => 'artisan',
        'commands' => [
            ['name' => 'cache:clear', 'params' => []],
        ],
    ],
    'config_clear' => [
        'label' => 'Run config:clear',
        'category' => 'Storage & Cache',
        'help' => 'Clear cached configuration.',
        'type' => 'artisan',
        'commands' => [
            ['name' => 'config:clear', 'params' => []],
        ],
    ],
    'route_clear' => [
        'label' => 'Run route:clear',
        'category' => 'Storage & Cache',
        'help' => 'Clear cached routes.',
        'type' => 'artisan',
        'commands' => [
            ['name' => 'route:clear', 'params' => []],
        ],
    ],
    'view_clear' => [
        'label' => 'Run view:clear',
        'category' => 'Storage & Cache',
        'help' => 'Clear compiled Blade views.',
        'type' => 'artisan',
        'commands' => [
            ['name' => 'view:clear', 'params' => []],
        ],
    ],
    'event_clear' => [
        'label' => 'Run event:clear',
        'category' => 'Storage & Cache',
        'help' => 'Clear cached events and listeners.',
        'type' => 'artisan',
        'commands' => [
            ['name' => 'event:clear', 'params' => []],
        ],
    ],
    'all_clear' => [
        'label' => 'Run All Clear Commands',
        'category' => 'Storage & Cache',
        'help' => 'Run all cache clearing commands in one pass.',
        'type' => 'artisan',
        'commands' => [
            ['name' => 'optimize:clear', 'params' => []],
            ['name' => 'cache:clear', 'params' => []],
            ['name' => 'config:clear', 'params' => []],
            ['name' => 'route:clear', 'params' => []],
            ['name' => 'view:clear', 'params' => []],
            ['name' => 'event:clear', 'params' => []],
        ],
    ],
    'storage_and_clear' => [
        'label' => 'Run storage:link + all clear commands',
        'category' => 'Storage & Cache',
        'help' => 'Repair public storage and clear all caches together.',
        'type' => 'artisan',
        'commands' => [
            ['name' => 'storage:link', 'params' => []],
            ['name' => 'optimize:clear', 'params' => []],
            ['name' => 'cache:clear', 'params' => []],
            ['name' => 'config:clear', 'params' => []],
            ['name' => 'route:clear', 'params' => []],
            ['name' => 'view:clear', 'params' => []],
            ['name' => 'event:clear', 'params' => []],
        ],
    ],
    'build_prep' => [
        'label' => 'Run Build Prep (Laravel caches)',
        'category' => 'Storage & Cache',
        'help' => 'Warm config, route, view, and event caches for deployment.',
        'type' => 'artisan',
        'commands' => [
            ['name' => 'config:cache', 'params' => []],
            ['name' => 'route:cache', 'params' => []],
            ['name' => 'view:cache', 'params' => []],
            ['name' => 'event:cache', 'params' => []],
            ['name' => 'optimize', 'params' => []],
        ],
    ],
    'sync_catalog' => [
        'label' => 'Queue Catalog Sync (background)',
        'category' => 'Catalog & Images',
        'help' => 'Queue TD SYNNEX catalog sync in the products-sync queue.',
        'type' => 'artisan',
        'commands' => [
            ['name' => 'tdsynnex:sync-priceavailability-products', 'params' => []],
        ],
    ],
    'sync_catalog_force' => [
        'label' => 'Queue Force Catalog Sync (background)',
        'category' => 'Catalog & Images',
        'help' => 'Queue a forced fresh TD SYNNEX catalog sync.',
        'type' => 'artisan',
        'commands' => [
            ['name' => 'tdsynnex:sync-priceavailability-products', 'params' => ['--force' => true]],
        ],
    ],
    'sync_catalog_force_inline' => [
        'label' => 'Run Force Catalog Sync Now (inline)',
        'category' => 'Catalog & Images',
        'help' => 'Run a blocking fresh TD SYNNEX catalog sync in this request using current env settings.',
        'type' => 'artisan',
        'commands' => [
            ['name' => 'tdsynnex:sync-priceavailability-products', 'params' => ['--force' => true, '--sync' => true]],
        ],
    ],
    'reset_tds_products' => [
        'label' => 'Delete TD SYNNEX Cached Products',
        'category' => 'Catalog & Images',
        'help' => 'Delete only TD SYNNEX rows from the local products table so the next sync can rebuild from source.',
        'type' => 'reset_tds_products',
    ],
    'reset_and_resync_catalog' => [
        'label' => 'Reset TD SYNNEX + Force Resync Now',
        'category' => 'Catalog & Images',
        'help' => 'Delete TD SYNNEX cached rows, clear cache/config, then run a forced inline catalog rebuild.',
        'type' => 'reset_and_resync_catalog',
    ],
    'sync_images' => [
        'label' => 'Queue Image Sync (and optional descriptions)',
        'category' => 'Catalog & Images',
        'help' => 'Queue image enrichment with Limit/Chunk, and optionally queue description backfill.',
        'type' => 'sync_images',
    ],
    'download_images_local' => [
        'label' => 'Queue Local Image Download (use Limit/Chunk below)',
        'category' => 'Catalog & Images',
        'help' => 'Queue download of external image URLs to local /images/products paths.',
        'type' => 'download_images_local',
    ],
    'sync_local_images_by_sku' => [
        'label' => 'Sync Existing Local Images by SKU (inline)',
        'category' => 'Catalog & Images',
        'help' => 'Scan public/images/products for SKU-named files and write local image URLs into products.images.',
        'type' => 'artisan',
        'commands' => [
            ['name' => 'products:sync-local-images-by-sku', 'params' => []],
        ],
    ],
    'sync_descriptions' => [
        'label' => 'Queue Description Backfill (use Limit/Chunk below)',
        'category' => 'Catalog & Images',
        'help' => 'Queue Icecat description backfill for products missing descriptions.',
        'type' => 'sync_descriptions',
    ],
    'db_repair' => [
        'label' => 'Repair DB Schema (run pending migrations)',
        'category' => 'Database Repairs',
        'help' => 'Run the schema repair command and pending migrations.',
        'type' => 'db_repair',
    ],
    'mela_chat_schema_fix' => [
        'label' => 'Run Mela AI Chat Schema Fix Migration',
        'category' => 'Database Repairs',
        'help' => 'Apply the chat sessions schema fix migration.',
        'type' => 'artisan',
        'commands' => [
            [
                'name' => 'migrate',
                'params' => [
                    '--force' => true,
                    '--path' => 'database/migrations/2026_04_10_120000_fix_chat_sessions_columns_for_assistant.php',
                ],
            ],
        ],
    ],
    'mela_chat_messages_id_fix' => [
        'label' => 'Run Mela AI Chat Messages ID Fix Migration',
        'category' => 'Database Repairs',
        'help' => 'Repair the chat_messages auto-increment primary key.',
        'type' => 'artisan',
        'commands' => [
            [
                'name' => 'migrate',
                'params' => [
                    '--force' => true,
                    '--path' => 'database/migrations/2026_04_10_130000_fix_chat_messages_id_autoincrement.php',
                ],
            ],
        ],
    ],
    'quotes_schema_fix' => [
        'label' => 'Run Quotes Table Fix Migration',
        'category' => 'Database Repairs',
        'help' => 'Create or repair the quotes table used by the assistant.',
        'type' => 'artisan',
        'commands' => [
            [
                'name' => 'migrate',
                'params' => [
                    '--force' => true,
                    '--path' => 'database/migrations/2026_04_10_140000_ensure_quotes_table_exists_for_assistant.php',
                ],
            ],
        ],
    ],
    'clear_tds_cache' => [
        'label' => 'Clear TD SYNNEX Cache',
        'category' => 'Catalog & Images',
        'help' => 'Clear cached TD SYNNEX catalog responses.',
        'type' => 'artisan',
        'commands' => [
            ['name' => 'tdsynnex:clear-cache', 'params' => []],
        ],
    ],
    'queue_products_sync_once' => [
        'label' => 'Process products-sync Queue (stop when empty)',
        'category' => 'Catalog & Images',
        'help' => 'Run queued products-sync jobs now and stop when queue is empty.',
        'type' => 'artisan',
        'commands' => [
            ['name' => 'queue:work', 'params' => ['connection' => 'database', '--queue' => 'products-sync', '--stop-when-empty' => true, '--sleep' => 1, '--tries' => 1, '--timeout' => 120]],
        ],
    ],
    'queue_failed_products_sync' => [
        'label' => 'Retry Failed products-sync Jobs',
        'category' => 'Catalog & Images',
        'help' => 'Retry failed queue jobs for products-sync.',
        'type' => 'artisan',
        'commands' => [
            ['name' => 'queue:retry', 'params' => ['id' => ['all']]],
        ],
    ],
    'vendor_report' => [
        'label' => 'Vendor Diagnostics Report',
        'category' => 'Diagnostics',
        'help' => 'Show vendor image coverage diagnostics from current products.',
        'type' => 'vendor_report',
    ],
    'admin_bootstrap' => [
        'label' => 'Admin Access Recovery (create/update admin user)',
        'category' => 'Access Recovery',
        'help' => 'Create or update an admin account with hashed password, verified email, active status, and approved company.',
        'type' => 'admin_bootstrap',
    ],
    'user_access_update' => [
        'label' => 'User Access Update (specific user)',
        'category' => 'Access Recovery',
        'help' => 'Grant or revoke access for a specific existing user by setting role, status, verification, and company approval.',
        'type' => 'user_access_update',
    ],
];

$groupedActions = [];
foreach ($actions as $actionKey => $action) {
    $category = (string) ($action['category'] ?? 'Other');
    $groupedActions[$category][$actionKey] = $action;
}

$selectedAction = isset($_POST['action']) ? (string) $_POST['action'] : 'status';
$selectedAction = array_key_exists($selectedAction, $actions) ? $selectedAction : 'status';

$results = [];

function toIntInRange(string $key, int $default, int $min, int $max): int
{
    $value = isset($_POST[$key]) ? (int) $_POST[$key] : $default;
    return max($min, min($max, $value));
}

function runArtisanCommand(string $command, array $params = []): array
{
    try {
        Artisan::call($command, $params);
        return [
            'command' => $command . (empty($params) ? '' : ' ' . json_encode($params)),
            'status' => 'OK',
            'output' => trim(Artisan::output()),
        ];
    } catch (Throwable $e) {
        return [
            'command' => $command . (empty($params) ? '' : ' ' . json_encode($params)),
            'status' => 'ERROR',
            'output' => $e->getMessage(),
        ];
    }
}

function toStringPost(string $key, string $default = ''): string
{
    return trim((string) ($_POST[$key] ?? $default));
}

function validateStrongPasswordForRecovery(string $password): ?string
{
    if (strlen($password) < 8) {
        return 'Password must be at least 8 characters.';
    }

    if (!preg_match('/[A-Z]/', $password)) {
        return 'Password must include at least one uppercase letter.';
    }

    if (!preg_match('/[a-z]/', $password)) {
        return 'Password must include at least one lowercase letter.';
    }

    if (!preg_match('/\d/', $password)) {
        return 'Password must include at least one number.';
    }

    if (!preg_match('/[^A-Za-z0-9]/', $password)) {
        return 'Password must include at least one symbol.';
    }

    return null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $actionType = (string) ($actions[$selectedAction]['type'] ?? 'status');

    if ($actionType === 'artisan') {
        foreach ((array) ($actions[$selectedAction]['commands'] ?? []) as $commandDef) {
            $commandName = (string) ($commandDef['name'] ?? '');
            $commandParams = (array) ($commandDef['params'] ?? []);
            if ($commandName === '') {
                continue;
            }

            $results[] = runArtisanCommand($commandName, $commandParams);
        }
    } elseif ($actionType === 'sync_images') {
        $limit = toIntInRange('sync_limit', 100, 0, 50000);
        $chunk = toIntInRange('sync_chunk', 25, 1, 500);
        $withDescriptions = isset($_POST['sync_with_descriptions']) && (string) $_POST['sync_with_descriptions'] === '1';

        EnrichPriceAvailabilityImagesJob::dispatch($chunk, $limit, true);
        $results[] = [
            'command' => 'queue: EnrichPriceAvailabilityImagesJob',
            'status' => 'OK',
            'output' => "Queued image enrichment in background (queue=products-sync, chunk={$chunk}, limit={$limit}).",
        ];

        if ($withDescriptions) {
            EnrichPriceAvailabilityDescriptionsJob::dispatch($chunk, $limit);
            $results[] = [
                'command' => 'queue: EnrichPriceAvailabilityDescriptionsJob',
                'status' => 'OK',
                'output' => "Queued description enrichment in background (queue=products-sync, chunk={$chunk}, limit={$limit}).",
            ];
        }
    } elseif ($actionType === 'download_images_local') {
        $limit = toIntInRange('sync_limit', 100, 0, 50000);
        $chunk = toIntInRange('sync_chunk', 1, 1, 500);

        DownloadProductImagesJob::dispatch($limit, $chunk);
        $results[] = [
            'command' => 'queue: DownloadProductImagesJob',
            'status' => 'OK',
            'output' => "Queued local image download in background (queue=products-sync, chunk={$chunk}, limit={$limit}).",
        ];
    } elseif ($actionType === 'sync_descriptions') {
        $limit = toIntInRange('sync_limit', 100, 0, 50000);
        $chunk = toIntInRange('sync_chunk', 25, 1, 500);

        EnrichPriceAvailabilityDescriptionsJob::dispatch($chunk, $limit);
        $results[] = [
            'command' => 'queue: EnrichPriceAvailabilityDescriptionsJob',
            'status' => 'OK',
            'output' => "Queued description enrichment in background (queue=products-sync, chunk={$chunk}, limit={$limit}).",
        ];
    } elseif ($actionType === 'db_repair') {
        $seed = isset($_POST['db_seed']) && $_POST['db_seed'] === '1';
        $params = [];
        if ($seed) {
            $params['--seed'] = true;
        }

        $results[] = runArtisanCommand('db:repair-schema', $params);
    } elseif ($actionType === 'vendor_report') {
        try {
            $rows = Product::query()
                ->where('vendor_id', 'TD SYNNEX')
                ->selectRaw("TRIM(COALESCE(JSON_UNQUOTE(JSON_EXTRACT(specifications, '$.manufacturer')), 'UNKNOWN')) as manufacturer")
                ->selectRaw("COUNT(*) as total_count")
                ->selectRaw("SUM(CASE WHEN images IS NULL OR images = '[]' THEN 1 ELSE 0 END) as missing_images")
                ->groupBy('manufacturer')
                ->orderByDesc('missing_images')
                ->limit(40)
                ->get();

            $lines = [];
            foreach ($rows as $row) {
                $lines[] = sprintf(
                    '%s | total=%d | missing_images=%d',
                    (string) $row->manufacturer,
                    (int) $row->total_count,
                    (int) $row->missing_images
                );
            }

            $results[] = [
                'command' => 'vendor_diagnostics',
                'status' => 'OK',
                'output' => empty($lines) ? 'No vendor rows found.' : implode("\n", $lines),
            ];
        } catch (Throwable $e) {
            $results[] = [
                'command' => 'vendor_diagnostics',
                'status' => 'ERROR',
                'output' => $e->getMessage(),
            ];
        }
    } elseif ($actionType === 'catalog_diagnostics') {
        try {
            $tdCount = Product::query()->where('vendor_id', 'TD SYNNEX')->count();
            $availableCount = Product::query()->where('vendor_id', 'TD SYNNEX')->where('is_available', 1)->count();
            $pricedCount = Product::query()->where('vendor_id', 'TD SYNNEX')->where('base_price', '>', 0)->count();
            $discontinuedCount = Product::query()->where('vendor_id', 'TD SYNNEX')->where('is_discontinued', 1)->count();

            $results[] = [
                'command' => 'catalog_diagnostics',
                'status' => 'OK',
                'output' => implode("\n", [
                    'TDSYNNEX_PRODUCTS_SOURCE=' . (string) config('tdsynnex.products_source', ''),
                    'TDSYNNEX_XML_USE_TEST=' . (config('tdsynnex.xml.use_test_by_default') ? 'true' : 'false'),
                    'SYNNEX_FLAT_FILE_PATH=' . (string) config('tdsynnex.price_availability.flat_file_path', ''),
                    'SYNNEX_FLAT_FILES_DIR=' . (string) config('tdsynnex.price_availability.flat_files_dir', ''),
                    'SYNNEX_MAX_SKUS=' . (string) config('tdsynnex.price_availability.max_skus', 0),
                    'CATALOG_HARDWARE_ONLY=' . (config('tdsynnex.catalog.hardware_only') ? 'true' : 'false'),
                    'TD_SYNNEX_DB_ROWS=' . (string) $tdCount,
                    'TD_SYNNEX_AVAILABLE_ROWS=' . (string) $availableCount,
                    'TD_SYNNEX_PRICED_ROWS=' . (string) $pricedCount,
                    'TD_SYNNEX_DISCONTINUED_ROWS=' . (string) $discontinuedCount,
                ]),
            ];
        } catch (Throwable $e) {
            $results[] = [
                'command' => 'catalog_diagnostics',
                'status' => 'ERROR',
                'output' => $e->getMessage(),
            ];
        }
    } elseif ($actionType === 'reset_tds_products') {
        try {
            $deleted = Product::query()->where('vendor_id', 'TD SYNNEX')->delete();
            Artisan::call('cache:clear');
            Artisan::call('config:clear');

            $results[] = [
                'command' => 'reset_tds_products',
                'status' => 'OK',
                'output' => "Deleted {$deleted} TD SYNNEX product rows and cleared cache/config.",
            ];
        } catch (Throwable $e) {
            $results[] = [
                'command' => 'reset_tds_products',
                'status' => 'ERROR',
                'output' => $e->getMessage(),
            ];
        }
    } elseif ($actionType === 'reset_and_resync_catalog') {
        try {
            $deleted = Product::query()->where('vendor_id', 'TD SYNNEX')->delete();

            $results[] = runArtisanCommand('cache:clear');
            $results[] = runArtisanCommand('config:clear');
            $results[] = runArtisanCommand('tdsynnex:sync-priceavailability-products', ['--force' => true, '--sync' => true]);

            array_unshift($results, [
                'command' => 'reset_tds_products',
                'status' => 'OK',
                'output' => "Deleted {$deleted} TD SYNNEX product rows before forced resync.",
            ]);
        } catch (Throwable $e) {
            $results[] = [
                'command' => 'reset_and_resync_catalog',
                'status' => 'ERROR',
                'output' => $e->getMessage(),
            ];
        }
    } elseif ($actionType === 'admin_bootstrap') {
        $name = toStringPost('admin_recovery_name', (string) env('ADMIN_NAME', 'Armely Admin'));
        $email = strtolower(toStringPost('admin_recovery_email', (string) env('ADMIN_EMAIL', '')));
        $password = (string) ($_POST['admin_recovery_password'] ?? '');
        $companyName = toStringPost('admin_recovery_company', 'Armely Internal');
        $role = strtolower(toStringPost('admin_recovery_role', 'admin'));

        $allowedRoles = ['admin', 'owner', 'manager'];
        if (!in_array($role, $allowedRoles, true)) {
            $role = 'admin';
        }

        if ($name === '' || $email === '' || $password === '') {
            $results[] = [
                'command' => 'admin_bootstrap',
                'status' => 'ERROR',
                'output' => 'Name, email, and password are required.',
            ];
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $results[] = [
                'command' => 'admin_bootstrap',
                'status' => 'ERROR',
                'output' => 'Please provide a valid admin email address.',
            ];
        } elseif (($passwordError = validateStrongPasswordForRecovery($password)) !== null) {
            $results[] = [
                'command' => 'admin_bootstrap',
                'status' => 'ERROR',
                'output' => $passwordError,
            ];
        } else {
            try {
                $domain = strtolower((string) substr(strrchr($email, '@') ?: '', 1));
                if ($domain === '') {
                    throw new RuntimeException('Unable to derive company domain from email.');
                }

                DB::beginTransaction();

                $company = Company::where('domain', $domain)->first();
                if (!$company) {
                    $company = Company::create([
                        'name' => $companyName,
                        'domain' => $domain,
                        'status' => 'approved',
                    ]);
                } else {
                    $company->forceFill([
                        'name' => $companyName !== '' ? $companyName : $company->name,
                        'status' => 'approved',
                    ])->save();
                }

                $user = User::where('email', $email)->first();
                $payload = [
                    'name' => $name,
                    'password' => Hash::make($password),
                    'company_id' => $company->id,
                    'role' => $role,
                    'status' => 'active',
                    'email_verified_at' => now(),
                ];

                $operation = 'updated';
                if (!$user) {
                    $user = User::create(array_merge($payload, ['email' => $email]));
                    $operation = 'created';
                } else {
                    $user->forceFill($payload)->save();
                }

                if (method_exists($user, 'tokens')) {
                    $user->tokens()->delete();
                }

                DB::commit();

                $results[] = [
                    'command' => 'admin_bootstrap',
                    'status' => 'OK',
                    'output' => sprintf(
                        'Admin user %s successfully.\nEmail: %s\nRole: %s\nStatus: %s\nEmail verified: YES\nCompany: %s (%s, approved)',
                        $operation,
                        $email,
                        $role,
                        'active',
                        (string) $company->name,
                        (string) $company->domain
                    ),
                ];
            } catch (Throwable $e) {
                DB::rollBack();

                $results[] = [
                    'command' => 'admin_bootstrap',
                    'status' => 'ERROR',
                    'output' => $e->getMessage(),
                ];
            }
        }
    } elseif ($actionType === 'user_access_update') {
        $email = strtolower(toStringPost('user_access_email', ''));
        $role = strtolower(toStringPost('user_access_role', 'user'));
        $status = strtolower(toStringPost('user_access_status', 'active'));
        $companyStatus = strtolower(toStringPost('user_access_company_status', 'approved'));
        $verifyEmail = isset($_POST['user_access_verify_email']) && (string) $_POST['user_access_verify_email'] === '1';

        $allowedRoles = ['user', 'buyer', 'owner', 'manager', 'admin'];
        $allowedUserStatuses = ['active', 'pending', 'inactive', 'suspended'];
        $allowedCompanyStatuses = ['approved', 'pending', 'rejected'];

        if ($email === '') {
            $results[] = [
                'command' => 'user_access_update',
                'status' => 'ERROR',
                'output' => 'User email is required.',
            ];
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $results[] = [
                'command' => 'user_access_update',
                'status' => 'ERROR',
                'output' => 'Please provide a valid user email address.',
            ];
        } else {
            if (!in_array($role, $allowedRoles, true)) {
                $role = 'user';
            }
            if (!in_array($status, $allowedUserStatuses, true)) {
                $status = 'active';
            }
            if (!in_array($companyStatus, $allowedCompanyStatuses, true)) {
                $companyStatus = 'approved';
            }

            try {
                DB::beginTransaction();

                $user = User::where('email', $email)->first();
                if (!$user) {
                    throw new RuntimeException('No user found with that email. Create the account first, then update access.');
                }

                $user->forceFill([
                    'role' => $role,
                    'status' => $status,
                    'email_verified_at' => $verifyEmail ? ($user->email_verified_at ?? now()) : null,
                ])->save();

                $companyName = 'n/a';
                $companyDomain = 'n/a';
                $companyAppliedStatus = 'n/a';

                if ($user->company_id) {
                    $company = Company::find($user->company_id);
                    if ($company) {
                        $company->forceFill([
                            'status' => $companyStatus,
                        ])->save();

                        $companyName = (string) $company->name;
                        $companyDomain = (string) $company->domain;
                        $companyAppliedStatus = (string) $company->status;
                    }
                }

                if (method_exists($user, 'tokens')) {
                    $user->tokens()->delete();
                }

                DB::commit();

                $results[] = [
                    'command' => 'user_access_update',
                    'status' => 'OK',
                    'output' => sprintf(
                        "User access updated.\nEmail: %s\nRole: %s\nUser status: %s\nEmail verified: %s\nCompany: %s (%s)\nCompany status: %s",
                        (string) $user->email,
                        (string) $user->role,
                        (string) $user->status,
                        $user->email_verified_at ? 'YES' : 'NO',
                        $companyName,
                        $companyDomain,
                        $companyAppliedStatus
                    ),
                ];
            } catch (Throwable $e) {
                DB::rollBack();

                $results[] = [
                    'command' => 'user_access_update',
                    'status' => 'ERROR',
                    'output' => $e->getMessage(),
                ];
            }
        }
    }
}

$publicStoragePath = public_path('storage');
$storagePublicPath = storage_path('app/public');
$cachePath = base_path('bootstrap/cache');
$chatSessionsExists = Schema::hasTable('chat_sessions');
$chatMessagesExists = Schema::hasTable('chat_messages');
$quotesTableExists = Schema::hasTable('quotes');
$chatMessagesIdHealthy = false;
$tdSynnexCount = Product::query()->where('vendor_id', 'TD SYNNEX')->count();
$tdSynnexAvailableCount = Product::query()->where('vendor_id', 'TD SYNNEX')->where('is_available', 1)->count();
$tdSynnexPricedCount = Product::query()->where('vendor_id', 'TD SYNNEX')->where('base_price', '>', 0)->count();

if ($chatMessagesExists) {
    $idMeta = DB::table('information_schema.columns')
        ->select(['COLUMN_KEY', 'EXTRA'])
        ->where('TABLE_SCHEMA', DB::getDatabaseName())
        ->where('TABLE_NAME', 'chat_messages')
        ->where('COLUMN_NAME', 'id')
        ->first();

    $chatMessagesIdHealthy = $idMeta
        && strtoupper((string) ($idMeta->COLUMN_KEY ?? '')) === 'PRI'
        && str_contains(strtolower((string) ($idMeta->EXTRA ?? '')), 'auto_increment');
}

$statusRows = [
    'App env' => (string) config('app.env'),
    'App debug' => config('app.debug') ? 'true' : 'false',
    'App URL' => (string) config('app.url'),
    'Frontend URL' => (string) env('FRONTEND_URL', ''),
    'Asset URL' => (string) config('app.asset_url'),
    'Default disk' => (string) config('filesystems.default'),
    'Public root' => public_path(),
    'Storage app/public' => $storagePublicPath,
    'public/storage exists' => file_exists($publicStoragePath) ? 'YES' : 'NO',
    'public/storage is link' => is_link($publicStoragePath) ? 'YES' : 'NO',
    'public/storage writable' => file_exists($publicStoragePath) ? (is_writable($publicStoragePath) ? 'YES' : 'NO') : 'NO',
    'storage/app/public exists' => file_exists($storagePublicPath) ? 'YES' : 'NO',
    'storage/app/public writable' => file_exists($storagePublicPath) ? (is_writable($storagePublicPath) ? 'YES' : 'NO') : 'NO',
    'bootstrap/cache writable' => file_exists($cachePath) ? (is_writable($cachePath) ? 'YES' : 'NO') : 'NO',
    'chat_sessions exists' => $chatSessionsExists ? 'YES' : 'NO',
    'chat_sessions.escalated_to_human' => ($chatSessionsExists && Schema::hasColumn('chat_sessions', 'escalated_to_human')) ? 'YES' : 'NO',
    'chat_sessions.escalated_at' => ($chatSessionsExists && Schema::hasColumn('chat_sessions', 'escalated_at')) ? 'YES' : 'NO',
    'chat_sessions.resolved_at' => ($chatSessionsExists && Schema::hasColumn('chat_sessions', 'resolved_at')) ? 'YES' : 'NO',
    'chat_messages exists' => $chatMessagesExists ? 'YES' : 'NO',
    'chat_messages.id auto_increment PK' => $chatMessagesIdHealthy ? 'YES' : 'NO',
    'quotes table exists' => $quotesTableExists ? 'YES' : 'NO',
    'TDSYNNEX_PRODUCTS_SOURCE' => (string) config('tdsynnex.products_source', ''),
    'TDSYNNEX_XML_USE_TEST' => config('tdsynnex.xml.use_test_by_default') ? 'true' : 'false',
    'SYNNEX_FLAT_FILE_PATH' => (string) config('tdsynnex.price_availability.flat_file_path', ''),
    'SYNNEX_FLAT_FILES_DIR' => (string) config('tdsynnex.price_availability.flat_files_dir', ''),
    'SYNNEX_MAX_SKUS' => (string) config('tdsynnex.price_availability.max_skus', 0),
    'TD SYNNEX DB rows' => (string) $tdSynnexCount,
    'TD SYNNEX available rows' => (string) $tdSynnexAvailableCount,
    'TD SYNNEX priced rows' => (string) $tdSynnexPricedCount,
    'Catalog hardware only' => config('tdsynnex.catalog.hardware_only') ? 'true' : 'false',
    'Image sync current showing only' => config('tdsynnex.image_sync.current_showing_only') ? 'true' : 'false',
    'Image sync scope cap' => (string) config('tdsynnex.image_sync.scope_cap', 1000),
];

$userAccessRows = User::query()
    ->select(['id', 'name', 'email', 'role', 'status', 'email_verified_at', 'company_id', 'updated_at'])
    ->with(['company:id,name,domain,status'])
    ->orderByDesc('updated_at')
    ->limit(30)
    ->get();

function h(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Armely Maintenance Utility</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f5f7fb;
            color: #1f2937;
        }
        .wrap {
            max-width: 1100px;
            margin: 0 auto;
            padding: 24px;
        }
        .card {
            background: #ffffff;
            border: 1px solid #dbe3ef;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
        }
        h1, h2 {
            margin-top: 0;
        }
        .control-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 14px;
            margin-bottom: 16px;
        }
        .sync-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 10px;
            margin-bottom: 16px;
        }
        .sync-options label {
            font-size: 13px;
            color: #4b5563;
            display: block;
            margin-bottom: 4px;
        }
        .sync-options input {
            width: 100%;
            padding: 10px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 14px;
        }
        .sync-options select {
            width: 100%;
            padding: 10px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 14px;
            background: #fff;
        }
        .primary-form {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        .submit-row {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
            padding: 12px 14px;
            border: 1px solid #dbe3ef;
            border-radius: 10px;
            background: #f8fbff;
        }
        .submit-row button {
            width: auto;
            min-width: 230px;
        }
        .submit-note {
            font-size: 13px;
            color: #475569;
        }
        .action-help {
            margin: 0;
            padding: 12px 14px;
            border-radius: 10px;
            background: #eff6ff;
            color: #1e3a8a;
            font-size: 14px;
            border: 1px solid #bfdbfe;
        }
        button {
            width: 100%;
            padding: 12px 14px;
            border: 0;
            border-radius: 10px;
            background: #1d4b8f;
            color: #ffffff;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
        }
        button:hover {
            background: #163a6f;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px 12px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
            vertical-align: top;
        }
        th {
            width: 260px;
            color: #4b5563;
        }
        pre {
            white-space: pre-wrap;
            word-break: break-word;
            background: #0f172a;
            color: #e2e8f0;
            padding: 14px;
            border-radius: 10px;
            overflow-x: auto;
        }
        .ok {
            color: #047857;
            font-weight: 700;
        }
        .error {
            color: #b91c1c;
            font-weight: 700;
        }
        .note {
            font-size: 14px;
            color: #6b7280;
        }
        .section-title {
            font-size: 13px;
            font-weight: 700;
            color: #475569;
            margin-bottom: 6px;
            display: block;
        }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="card">
            <h1>Armely Maintenance Utility</h1>
            <p class="note">Use this page to inspect storage status and run maintenance commands when terminal access is unavailable.</p>
        </div>

        <div class="card">
            <h2>Actions</h2>
            <form method="post" class="primary-form">
                <div class="control-grid">
                    <div>
                        <label class="section-title" for="action">Primary Operation</label>
                        <select id="action" name="action">
                            <?php foreach ($groupedActions as $category => $categoryActions): ?>
                                <optgroup label="<?= h($category) ?>">
                                    <?php foreach ($categoryActions as $actionKey => $action): ?>
                                        <option value="<?= h($actionKey) ?>" <?= $selectedAction === $actionKey ? 'selected' : '' ?>><?= h($action['label']) ?></option>
                                    <?php endforeach; ?>
                                </optgroup>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="section-title" for="sync_limit">Catalog/Image Limit (0 = all scoped)</label>
                        <input type="number" id="sync_limit" name="sync_limit" value="<?= h((string) ($_POST['sync_limit'] ?? '100')) ?>" min="0" max="50000">
                    </div>
                    <div>
                        <label class="section-title" for="sync_chunk">Catalog/Image Chunk</label>
                        <input type="number" id="sync_chunk" name="sync_chunk" value="<?= h((string) ($_POST['sync_chunk'] ?? '25')) ?>" min="1" max="500">
                    </div>
                    <div>
                        <label class="section-title" for="sync_with_descriptions">Image Sync Options</label>
                        <div style="display:flex;align-items:center;gap:8px;padding:10px;border:1px solid #cbd5e1;border-radius:8px;background:#fff;min-height:44px;">
                            <input type="checkbox" id="sync_with_descriptions" name="sync_with_descriptions" value="1" <?= isset($_POST['sync_with_descriptions']) ? 'checked' : '' ?>>
                            <span style="font-size:14px;color:#374151;">Also queue description backfill with image sync</span>
                        </div>
                    </div>
                    <div>
                        <label class="section-title" for="db_seed">DB Repair Options</label>
                        <div style="display:flex;align-items:center;gap:8px;padding:10px;border:1px solid #cbd5e1;border-radius:8px;background:#fff;min-height:44px;">
                            <input type="checkbox" id="db_seed" name="db_seed" value="1" <?= isset($_POST['db_seed']) && $_POST['db_seed'] === '1' ? 'checked' : '' ?>>
                            <span style="font-size:14px;color:#374151;">Include seeders (db:seed)</span>
                        </div>
                    </div>
                    <div>
                        <label class="section-title" for="admin_recovery_name">Admin Name</label>
                        <input type="text" id="admin_recovery_name" name="admin_recovery_name" value="<?= h((string) ($_POST['admin_recovery_name'] ?? env('ADMIN_NAME', 'Armely Admin'))) ?>" placeholder="Armely Admin">
                    </div>
                    <div>
                        <label class="section-title" for="admin_recovery_email">Admin Email</label>
                        <input type="email" id="admin_recovery_email" name="admin_recovery_email" value="<?= h((string) ($_POST['admin_recovery_email'] ?? env('ADMIN_EMAIL', ''))) ?>" placeholder="admin@company.com">
                    </div>
                    <div>
                        <label class="section-title" for="admin_recovery_password">Admin Password</label>
                        <input type="password" id="admin_recovery_password" name="admin_recovery_password" value="" placeholder="Strong password (8+, upper/lower/number/symbol)">
                    </div>
                    <div>
                        <label class="section-title" for="admin_recovery_company">Admin Company Name</label>
                        <input type="text" id="admin_recovery_company" name="admin_recovery_company" value="<?= h((string) ($_POST['admin_recovery_company'] ?? 'Armely Internal')) ?>" placeholder="Armely Internal">
                    </div>
                    <div>
                        <label class="section-title" for="admin_recovery_role">Admin Role</label>
                        <select id="admin_recovery_role" name="admin_recovery_role">
                            <?php $selectedRole = strtolower((string) ($_POST['admin_recovery_role'] ?? 'admin')); ?>
                            <option value="admin" <?= $selectedRole === 'admin' ? 'selected' : '' ?>>admin</option>
                            <option value="owner" <?= $selectedRole === 'owner' ? 'selected' : '' ?>>owner</option>
                            <option value="manager" <?= $selectedRole === 'manager' ? 'selected' : '' ?>>manager</option>
                        </select>
                    </div>
                    <div>
                        <label class="section-title" for="user_access_email">User Email (specific user)</label>
                        <input type="email" id="user_access_email" name="user_access_email" value="<?= h((string) ($_POST['user_access_email'] ?? '')) ?>" placeholder="user@company.com">
                    </div>
                    <div>
                        <label class="section-title" for="user_access_role">User Role</label>
                        <select id="user_access_role" name="user_access_role">
                            <?php $selectedUserRole = strtolower((string) ($_POST['user_access_role'] ?? 'user')); ?>
                            <option value="user" <?= $selectedUserRole === 'user' ? 'selected' : '' ?>>user</option>
                            <option value="buyer" <?= $selectedUserRole === 'buyer' ? 'selected' : '' ?>>buyer</option>
                            <option value="owner" <?= $selectedUserRole === 'owner' ? 'selected' : '' ?>>owner</option>
                            <option value="manager" <?= $selectedUserRole === 'manager' ? 'selected' : '' ?>>manager</option>
                            <option value="admin" <?= $selectedUserRole === 'admin' ? 'selected' : '' ?>>admin</option>
                        </select>
                    </div>
                    <div>
                        <label class="section-title" for="user_access_status">User Status</label>
                        <select id="user_access_status" name="user_access_status">
                            <?php $selectedUserStatus = strtolower((string) ($_POST['user_access_status'] ?? 'active')); ?>
                            <option value="active" <?= $selectedUserStatus === 'active' ? 'selected' : '' ?>>active</option>
                            <option value="pending" <?= $selectedUserStatus === 'pending' ? 'selected' : '' ?>>pending</option>
                            <option value="inactive" <?= $selectedUserStatus === 'inactive' ? 'selected' : '' ?>>inactive</option>
                            <option value="suspended" <?= $selectedUserStatus === 'suspended' ? 'selected' : '' ?>>suspended</option>
                        </select>
                    </div>
                    <div>
                        <label class="section-title" for="user_access_company_status">Company Status</label>
                        <select id="user_access_company_status" name="user_access_company_status">
                            <?php $selectedCompanyStatus = strtolower((string) ($_POST['user_access_company_status'] ?? 'approved')); ?>
                            <option value="approved" <?= $selectedCompanyStatus === 'approved' ? 'selected' : '' ?>>approved</option>
                            <option value="pending" <?= $selectedCompanyStatus === 'pending' ? 'selected' : '' ?>>pending</option>
                            <option value="rejected" <?= $selectedCompanyStatus === 'rejected' ? 'selected' : '' ?>>rejected</option>
                        </select>
                    </div>
                    <div>
                        <label class="section-title" for="user_access_verify_email">Email Verification</label>
                        <div style="display:flex;align-items:center;gap:8px;padding:10px;border:1px solid #cbd5e1;border-radius:8px;background:#fff;min-height:44px;">
                            <input type="checkbox" id="user_access_verify_email" name="user_access_verify_email" value="1" <?= isset($_POST['user_access_verify_email']) && (string) $_POST['user_access_verify_email'] === '1' ? 'checked' : '' ?>>
                            <span style="font-size:14px;color:#374151;">Mark email as verified (required for login)</span>
                        </div>
                    </div>
                </div>

                <div class="submit-row">
                    <button type="submit">Run Selected Operation</button>
                    <span class="submit-note">Changes are not auto-saved. Enter details, then click this button.</span>
                </div>

                <p class="action-help"><?= h((string) ($actions[$selectedAction]['help'] ?? 'Select an operation to run maintenance tasks.')) ?></p>
            </form>
        </div>

        <div class="card">
            <h2>Status</h2>
            <table>
                <tbody>
                <?php foreach ($statusRows as $label => $value): ?>
                    <tr>
                        <th><?= h($label) ?></th>
                        <td><?= h((string) $value) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="card">
            <h2>User Access Table (Latest 30)</h2>
            <p class="note">Use this table to find the exact email, then run <strong>User Access Update (specific user)</strong> from Primary Operation.</p>
            <table>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>User Status</th>
                    <th>Email Verified</th>
                    <th>Company</th>
                    <th>Company Status</th>
                    <th>Updated</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($userAccessRows as $row): ?>
                    <tr>
                        <td><?= h((string) $row->id) ?></td>
                        <td><?= h((string) $row->name) ?></td>
                        <td><?= h((string) $row->email) ?></td>
                        <td><?= h((string) $row->role) ?></td>
                        <td><?= h((string) $row->status) ?></td>
                        <td><?= $row->email_verified_at ? 'YES' : 'NO' ?></td>
                        <td><?= h((string) optional($row->company)->name) ?></td>
                        <td><?= h((string) optional($row->company)->status) ?></td>
                        <td><?= h((string) $row->updated_at) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="card">
            <h2>Results</h2>
            <?php if (!$results): ?>
                <p class="note">No command has been run yet. Click any action above.</p>
            <?php else: ?>
                <?php foreach ($results as $result): ?>
                    <p>
                        <strong><?= h($result['command']) ?></strong>
                        <span class="<?= $result['status'] === 'OK' ? 'ok' : 'error' ?>"><?= h($result['status']) ?></span>
                    </p>
                    <pre><?= h($result['output'] !== '' ? $result['output'] : '[no output]') ?></pre>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>