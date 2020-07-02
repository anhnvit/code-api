<?php

namespace App\Providers;


use App\Repositories\AbstractBaseRepository;
use App\Repositories\FavouriteLocation\FavouriteLocationInterface;
use App\Repositories\FavouriteLocation\FavouriteLocationRepository;
use App\Repositories\FeedBack\FeedBackInterface;
use App\Repositories\FeedBack\FeedBackRepository;
use App\Repositories\RepositoryInterface;
use App\Repositories\Users\UserInterface;
use App\Repositories\Users\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
//        $this->registerTranslations();
//        $this->registerConfig();

    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(UserInterface::class, UserRepository::class);
        $this->app->bind(FavouriteLocationInterface::class, FavouriteLocationRepository::class);
        $this->app->bind(FeedBackInterface::class, FeedBackRepository::class);
    }
}
