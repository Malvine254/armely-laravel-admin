<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\User;
use App\Services\AzureGraphMailService;
use App\Support\OfferPricing;
use Illuminate\Console\Command;

/**
 * Sends one sample of every marketing reminder to a store account so the team
 * can see exactly what customers receive, without waiting for a real trigger.
 * Nothing is recorded: daily caps, sequences and idempotency are untouched.
 */
class SendTestLifecycleEmailsCommand extends Command
{
    protected $signature = 'lifecycle:send-test {email : Email of an existing store account to receive the samples}';

    protected $description = 'Send sample cart, viewed, favorite, price-drop and search reminders to one account';

    public function handle(AzureGraphMailService $mailer): int
    {
        $email = strtolower(trim((string) $this->argument('email')));
        $user = User::query()->whereRaw('LOWER(email) = ?', [$email])->first();
        if (!$user) {
            $this->error("No store account uses {$email}. Use the email of an existing store login.");
            return self::FAILURE;
        }

        $health = $mailer->configurationHealth();
        if (!$health['configured']) {
            $this->error('Azure Graph mail is not configured; missing: ' . implode(', ', $health['missing_fields']));
            return self::FAILURE;
        }

        $products = Product::query()
            ->where('is_available', true)
            ->where(fn ($query) => $query->where('is_discontinued', false)->orWhereNull('is_discontinued'))
            ->where('quantity', '>', 0)
            ->where('base_price', '>', 0)
            ->whereNotNull('images')
            ->orderByDesc('is_storefront_curated')
            ->limit(80)
            ->get()
            ->filter(fn (Product $product): bool => OfferPricing::sellPrice($product) > 0 && $mailer->canFeatureProductInMarketing($product))
            ->take(6)
            ->values();

        if ($products->isEmpty()) {
            $this->error('No in-stock products with a usable photo were found to use in the samples.');
            return self::FAILURE;
        }

        $first = $products->first();
        $price = OfferPricing::sellPrice($first);
        $previous = round($price * 1.12, 2);

        $samples = [
            'Viewed items digest' => fn () => $mailer->sendViewedProductsDigestEmail($user, $products->all()),
            'Single viewed item' => fn () => $mailer->sendViewedProductReminderEmail($user, $first, now()->subDay(), $price),
            'Saved (favorite) item' => fn () => $mailer->sendFavoriteProductReminderEmail($user, $first, now()->subDay(), $price),
            'Price drop' => fn () => $mailer->sendPriceDropAlertEmail($user, $first, $previous, $price, round($previous - $price, 2), round((($previous - $price) / $previous) * 100, 2)),
            'Abandoned cart' => fn () => $mailer->sendAbandonedCartReminderEmail($user, $products->take(2)->map(fn (Product $product) => [
                'product_id' => (int) $product->id,
                'product_name' => (string) $product->product_name,
                'mfg_part_no' => (string) $product->mfg_part_no,
                'quantity' => 1,
                'unit_price' => OfferPricing::sellPrice($product),
                'line_total' => OfferPricing::sellPrice($product),
                'image_url' => null,
                'product_url' => null,
            ])->all(), now()->subHours(3)),
            'Search follow-up' => fn () => $mailer->sendSearchReminderEmail($user, (string) strtok((string) $first->product_name, ' '), $products->take(3)->all()),
        ];

        $rows = [];
        $allSent = true;
        foreach ($samples as $label => $send) {
            try {
                $sent = (bool) $send();
            } catch (\Throwable $error) {
                $sent = false;
                $label .= ' (' . $error->getMessage() . ')';
            }
            $allSent = $allSent && $sent;
            $rows[] = [$label, $sent ? 'accepted by Microsoft' : 'NOT sent'];
        }

        $this->table(['Sample', 'Result'], $rows);
        $this->line("Sent to {$user->email}. 'Accepted' means Microsoft took the message; check the inbox and spam folder.");

        return $allSent ? self::SUCCESS : self::FAILURE;
    }
}
