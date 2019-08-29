<?php

namespace LangImportExport\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use LangImportExport\Facades\LangListService;

class ValidationCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lang:validate 
                            {target? : Locale to be checked.} 
    						{--l|locale= : Base locale (default - base locale from config).} 
    						{--g|group= : The name of translation file to export (default - base group from config).}
    						{--m|missing : Show missing translations}
    						';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Check language files for missing or misspelled placeholders";

    /**
     * Execute the console command.
     *
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        $baseLocale = $this->option('locale') ?: config('lang_import_export.base_locale');
        $groups = $this->option('group') ?: config('lang_import_export.base_group');
        $baseTranslations = LangListService::loadLangList($baseLocale, $groups);
        $target = $this->argument('target');
        if (empty($target)) {
            $this->error('--target is required');
        }
        foreach ($this->strToArray($target) as $locale) {
            $targetTranslations = LangListService::loadLangList($locale, $groups);
            $this->validatePlaceholders($targetTranslations, $baseTranslations, $locale);
            if ($this->option('missing')) {
                $this->showMissing($targetTranslations, $baseTranslations, $locale);
            }
        }
    }

    private function strToArray($string, $fallback = [])
    {
        if (!$string) {
            return $fallback;
        }
        return array_filter(array_map('trim', explode(',', $string)));
    }

    /**
     * @param $targetTranslations
     * @param $baseTranslations
     * @param $locale
     */
    private function validatePlaceholders($targetTranslations, $baseTranslations, $locale)
    {
        $this->info('Searching for missing placeholers...');
        foreach (LangListService::validatePlaceholders($targetTranslations, $baseTranslations) as $errors) {
            $this->warn($locale . "/{$errors['group']}.{$errors['key']} is missing \"{$errors['placeholder']}\".");
            $this->info($errors['translation'], 'v');
            $this->info($errors['baseTranslation'], 'vv');
        }
    }

    private function showMissing($targetTranslations, $baseTranslations, $locale)
    {
        $this->info('Searching for missing keys...');
        foreach ($baseTranslations as $group => $translations) {
            if (!isset($targetTranslations[$group])) {
                $this->warn("$locale/$group entire group is missing");
                continue;
            }
            foreach ($translations as $key => $translation) {
                if (!empty($baseTranslations[$group][$key]) && !isset($targetTranslations[$group][$key])) {
                    $this->warn("$locale/$group.$key is missing");
                }
            }
        }
    }
}
