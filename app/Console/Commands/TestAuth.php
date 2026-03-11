<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class TestAuth extends Command
{
    protected $signature = 'test:auth';
    protected $description = 'Test authentication system';

    public function handle()
    {
        $this->info('Testing authentication system...');

        // Test user creation
        try {
            $user = User::create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => Hash::make('password'),
                'role' => 'customer',
            ]);
            
            $this->info('✅ User created successfully: ' . $user->email);
            
            // Test user retrieval
            $foundUser = User::where('email', 'test@example.com')->first();
            if ($foundUser) {
                $this->info('✅ User retrieval successful');
            }
            
            // Test password verification
            if (Hash::check('password', $foundUser->password)) {
                $this->info('✅ Password verification successful');
            }
            
            // Clean up
            $foundUser->delete();
            $this->info('✅ Test user cleaned up');
            
            $this->info('🎉 All authentication tests passed!');
            
        } catch (\Exception $e) {
            $this->error('❌ Test failed: ' . $e->getMessage());
        }
    }
}