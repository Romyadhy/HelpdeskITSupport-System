<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View as Viewview;
use View;

class Dashboard extends Page
{
    // public static ?string $navigationIcon = 'heroicon-o-home';
    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-home';
    }
   
    protected string $view = 'filament.pages.dashboard';
}
