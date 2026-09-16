<?php

namespace Tests\Unit;

use App\Support\LoginDevice;
use PHPUnit\Framework\TestCase;

class LoginDeviceTest extends TestCase
{
    public function test_edge_is_not_mistaken_for_chrome(): void
    {
        $device = LoginDevice::describe('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/130.0.0.0 Safari/537.36 Edg/130.0.0.0');
        $this->assertSame('Microsoft Edge', $device['browser']);
        $this->assertSame('Windows', $device['platform']);
        $this->assertSame('Computer', $device['device']);
    }

    public function test_android_tablets_and_phones_are_distinguished(): void
    {
        $this->assertSame('Tablet', LoginDevice::describe('Mozilla/5.0 (Linux; Android 13) Chrome/130.0 Safari/537.36')['device']);
        $this->assertSame('Mobile phone', LoginDevice::describe('Mozilla/5.0 (Linux; Android 13) Chrome/130.0 Mobile Safari/537.36')['device']);
    }

    public function test_missing_history_metadata_is_not_invented(): void
    {
        $device = LoginDevice::describe(null);
        $this->assertSame('Device not recorded', $device['device']);
        $this->assertSame('Browser not recorded', $device['browser']);
        $this->assertSame('OS not recorded', $device['platform']);
    }
}
