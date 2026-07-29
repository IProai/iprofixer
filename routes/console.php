<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('iprofixer:about', function (): void {
    $this->info('IProFixer application foundation is installed.');
})->purpose('Confirm the IProFixer application runtime is available.');
