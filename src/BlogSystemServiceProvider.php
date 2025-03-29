<?php

namespace YourVendor\BlogSystem;

use Illuminate\Support\ServiceProvider;

class BlogSystemServiceProvider extends ServiceProvider
{
    public function boot()
{
    $this->loadRoutesFrom(__DIR__.'/routes/api.php');
    $this->loadMigrationsFrom(__DIR__.'/database/migrations');

    // اجرای مایگریشن‌های Spatie Media Library همراه با پکیج ما
    if ($this->app->runningInConsole()) {
        $this->publishes([
            __DIR__.'/../config/blog.php' => config_path('blog.php'),
        ], 'blog-config');

        $this->commands([
            \Spatie\MediaLibrary\Commands\CleanCommand::class,
            \Spatie\MediaLibrary\Commands\ClearCommand::class,
            \Spatie\MediaLibrary\Commands\RegenerateCommand::class,
        ]);

        $this->callAfterResolving('migrator', function ($migrator) {
            $migrator->run(database_path('migrations/spatie_medialibrary'));
        });
    }
}

    public function register()
    {
        // بارگذاری تنظیمات پکیج
        $this->mergeConfigFrom(__DIR__.'/Config/blog.php', 'blog');
    }
}
