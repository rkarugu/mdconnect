<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\TestEmail;

class EmailHealthCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:health-check {--email= : Admin email to send the report to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check email system health and send a report to administrators';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $adminEmail = $this->option('email') ?? config('mail.from.address');
        
        $this->info('Running email health check...');
        
        try {
            // Check mail configuration
            $this->info('Checking mail configuration...');
            
            $mailConfig = [
                'Driver' => config('mail.default'),
                'Host' => config('mail.mailers.smtp.host'),
                'Port' => config('mail.mailers.smtp.port'),
                'From Address' => config('mail.from.address'),
                'From Name' => config('mail.from.name'),
            ];
            
            $configStatus = true;
            foreach ($mailConfig as $key => $value) {
                if (empty($value)) {
                    $this->error("Mail configuration issue: {$key} is not set");
                    $configStatus = false;
                } else {
                    $this->info("Mail configuration: {$key} = {$value}");
                }
            }
            
            if (!$configStatus) {
                $this->error('Mail configuration is incomplete. Please check your .env file.');
                return 1;
            }
            
            // Test sending an email
            $this->info("Sending test email to {$adminEmail}...");
            Mail::to($adminEmail)->send(new TestEmail());
            
            $this->info('Email health check completed successfully.');
            
            // Log the success
            Log::info('Email health check completed successfully. Report sent to ' . $adminEmail);
            
            return 0;
        } catch (\Exception $e) {
            $this->error('Email health check failed: ' . $e->getMessage());
            
            // Log the error
            Log::error('Email health check failed: ' . $e->getMessage());
            
            return 1;
        }
    }
}
