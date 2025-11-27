<?php

namespace App\Providers;

use App\Domain\TrashGame\Domain\Contracts\LayoutFactoryInterface;
use App\Domain\TrashGame\Domain\Contracts\SlotFactoryInterface;
use App\Domain\TrashGame\Infrastructure\LayoutFactory;
use App\Domain\TrashGame\Infrastructure\SlotFactory;
use Illuminate\Support\ServiceProvider;

class GameServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(SlotFactoryInterface::class, SlotFactory::class);
        $this->app->bind(LayoutFactoryInterface::class, LayoutFactory::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
