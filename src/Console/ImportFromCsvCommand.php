<?php

namespace LangImportExport\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use LangImportExport\Facades\LangListService;

class ImportFromCsvCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lang:import
    						{input : Filename of file to be imported with translation files.}
							{--l|locale= : The locale to be imported (default - parsed from file name).} 
    						{--g|group= : The name of translation file to imported (default - base group from config).} 
    						{--p|placeholders : Search for missing placeholders in imported keys.} 
    						{--column-map= : Map columns if other columns are used for notes (e.g. "0,1,3").}
    						{--D|delimiter=, : Field delimiter (optional, default - ",").} 
    						{--E|enclosure=" : Field enclosure (optional, default - \'"\').} 
    						{--escape=" : Field escape (optional, default - \'"\').}
    						{--X|excel : Set file encoding from Excel (optional, default - UTF-8).}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Imports the CSV file and write content into language files.";

    public function handle()
    {
        $fileName = $this->argument('input');
        $locale = $this->option('locale');
        if (!$locale) {
            $locale = pathinfo($fileName, PATHINFO_FILENAME);
            if (file_exists(resource_path("lang/$locale"))) {
                $this->info("Detected locale $locale");
            } else {
                $this->error("Could not detect locale of $fileName");
                return 1;
            }
        }
        $translations = $this->readTranslations($fileName);
        $group = $this->option('group');
        LangListService::writeLangList($locale, $group, $translations);
        if ($this->option('placeholders')) {
            $baseTranslations = LangListService::loadLangList(config('lang_import_export.base_locale'), $group);
            foreach (LangListService::validatePlaceholders($translations, $baseTranslations) as $errors) {
                $this->warn($locale . "/{$errors['group']}.{$errors['key']} is missing \"{$errors['placeholder']}\".");
                $this->info($errors['translation'], 'v');
                $this->info($errors['baseTranslation'], 'vv');
            }
        }
    }


    /**
     * Get translations from CSV file.
     *
     * @return array
     * @throws \Exception
     */
    private function readTranslations($fileName)
    {
        if (($input = fopen($fileName, 'r')) === false) {
            throw new \Exception('File can not be opened ' . $fileName);
        }
        $translations = $this->readFile($input);
        fclose($input);

        return $translations;
    }

    /**
     * Read content of file.
     *
     * @param resource $input
     * @return array
     * @throws \Exception
     */
    private function readFile($input)
    {
        if ($this->option('excel')) {
            $this->adjustFromExcel();
        }

        $translations = [];
        $confirmed = false;
        $map = explode(',', $this->option('column-map') ?: '0,1,2');
        while (($data = fgetcsv(
                $input, 0, $this->option('delimiter'), $this->option('enclosure'), $this->option('escape'))
            ) !== false) {
            if (isset($translations[$data[0]]) == false) {
                $translations[$data[0]] = [];
            }
            $columns = sizeof($data);
            if ($columns < 3) {
                throw new \Exception("File has only $columns column/s");
            }
            if ($columns > 3 && !$this->option('column-map')) {
                throw new \Exception("File has $columns columns");
            }
            $translations[$data[$map[0]]][$data[$map[1]]] = $data[$map[2]];
        }

        return $translations;
    }

    /**
     * Adjust file to Excel format.
     *
     * @return void
     */
    private function adjustFromExcel()
    {
        $data = file_get_contents($this->argument('input'));
        file_put_contents($this->argument('input'), mb_convert_encoding($data, 'UTF-8', 'UTF-16'));
    }
}
