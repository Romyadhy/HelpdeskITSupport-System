<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('app:send-report-reminder')
    ->everyMinute();    
    // ->dailyAt('11:20')
    // ->timezone('Asia/Makassar');
