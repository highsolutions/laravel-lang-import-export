<?php

namespace LangImportExport;

use Illuminate\Support\ServiceProvider;
use LangImportExport\Console\CheckPlaceholdersCommand;
use LangImportExport\Console\ExportToCsvCommand;
use LangImportExport\Console\ImportFromCsvCommand;
use LangImportExport\Console\ValidationCommand;

class LangImportExportServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/config/lang_import_export.php' => config_path('lang_import_export.php'),
        ]);
        if ($this->app->runningInConsole()) {
            $this->commands([
                ImportFromCsvCommand::class,
                ExportToCsvCommand::class,
                ValidationCommand::class,
            ]);
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/config/lang_import_export.php', 'lang_import_export');
        $this->app->singleton('LangImportExportLangListService', function () {
            return new LangListService;
        });
    }
}
