<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AzureGraphMailService;
use App\Support\FrontendUrl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminAccountController extends Controller
{
    private function authorizeAccount(Request $request, User $account, bool $write = false): void
    {
        $actor = $request->user();
        abort_unless(in_array($actor->role, ['admin', 'super_admin']), 403);
        $adminAccount = in_array($account->role, ['admin', 'super_admin']);
        if ($adminAccount && $write) {
            abort_unless($actor->role === 'super_admin', 403, 'Only super admins can manage administrator accounts.');
        }
        $permission = $adminAccount ? 'manage_admins' : 'manage_customers';
        abort_unless($actor->role === 'super_admin' || empty($actor->permissions)
            || in_array($permission, $actor->permissions), 403);
    }

    public function show(Request $request, User $account)
    {
        $this->authorizeAccount($request, $account);
        $account->load('company.addresses');
        return response()->json(['success' => true, 'data' => $account->only([
            'id', 'name', 'email', 'phone', 'role', 'status', 'email_verified_at',
            'created_at', 'updated_at', 'force_password_change', 'temp_password_expires_at',
            'permissions', 'special_pricing_percent', 'assigned_shipping_amount', 'company',
        ]), 'can_manage' => !in_array($account->role, ['admin', 'super_admin'])
            || $request->user()->role === 'super_admin']);
    }

    public function update(Request $request, User $account)
    {
        $this->authorizeAccount($request, $account, true);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($account->id)],
            'phone' => ['nullable', 'string', 'max:50'],
        ]);
        DB::transaction(function () use ($account, $data) {
            if ($account->email !== $data['email']) {
                DB::table('password_reset_tokens')->where('email', $account->email)->delete();
                $account->email_verified_at = null;
                $account->tokens()->delete();
            }
            $account->fill($data)->save();
        });
        Log::info('Admin updated account profile', ['actor_id' => $request->user()->id, 'account_id' => $account->id]);
        return $this->show($request, $account->fresh());
    }

    public function sendReset(Request $request, User $account, AzureGraphMailService $mail)
    {
        $this->authorizeAccount($request, $account, true);
        $key = 'admin-account-reset:' . $account->id;
        abort_unless(Cache::add($key, true, 60), 429, 'Please wait a minute before sending another reset link.');
        $token = Str::random(64);
        $hash = Hash::make($token);
        DB::table('password_reset_tokens')->updateOrInsert(['email' => $account->email], [
            'token' => $hash, 'created_at' => now(),
        ]);
        $url = FrontendUrl::base() . '/reset-password?token=' . urlencode($token) . '&email=' . urlencode($account->email);
        try {
            $sent = $mail->sendPasswordResetEmail($account->email, $account->name, $url);
        } catch (\Throwable $e) {
            $sent = false;
        }
        if (!$sent) {
            DB::table('password_reset_tokens')->where('email', $account->email)->where('token', $hash)->delete();
            Cache::forget($key);
            return response()->json(['success' => false, 'message' => 'The reset email could not be sent. Please try again.'], 502);
        }
        Log::info('Admin sent account reset email', ['actor_id' => $request->user()->id, 'account_id' => $account->id]);
        return response()->json(['success' => true, 'message' => 'Password reset link sent. The link expires in 60 minutes.']);
    }
}
