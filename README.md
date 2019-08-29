![Laravel-Lang-Import-Export v6](https://raw.githubusercontent.com/AidasK/laravel-lang-import-export/master/logo.png)

Laravel-Lang-Import-Export **(Refactored)**
==========================

This package provides artisan commands to import and export language files from and to CSV. This can be used to send translations to agencies that normally work with Excel-like files. In practice, CSV format is supper easy to work with for any translator in Fiverr or for any other freelancer. Personally, I have tried every other format such as **php, yaml, docx, pod, txt** and all of them has too complex syntax and requires custom software to work with (Not to mention all those problems with file encodings). CSV solves it all!

# How It Works? 

It turns some navigation.php file...

```php
<?php

return array (
  'commands' =>
  array (
    'next' => 'Next',
    'prev' => 'Previous',
    'play' => 'Play',
  ),
  'tips' =>
  array (
    'next' => 'Navigate to the next item',
    'prev' => 'Navigate to the previous item',
    'play' => 'Autoplay the slide show',
  ),
);
```
...to the following CSV...

```CSV
navigation.commands.next,Next
navigation.commands.prev,Previous
navigation.commands.play,Play
navigation.tips.next,"Navigate to the next item"
navigation.tips.prev,"Navigate to the previous item"
navigation.tips.play,"Autoplay the slide show"

```
...and vice versa.

Installation
------------

```sh
    composer require aidask/laravel-lang-import-export
```

This package uses Laravel 5.5 Package Auto-Discovery.
For previous versions of Laravel, you need to update `config/app.php` by adding an entry for the service provider:

```php
    'providers' => array(
        /* ... */
        'LangImportExport\LangImportExportServiceProvider'
    )
```

Usage
-----

The package currently provides two commands, one for exporting the files and one for importing them back:

### Export

```bash
php artisan lang:export --locale en
php artisan lang:export --locale en --target fr,de,pt  # export en translations only missing in fr,de,pt locales. Each in separate files
php artisan lang:export -l fr,de,pt -z all.zip  # archive all the files
php artisan lang:export --locale en -g paggination,validation  # export only cretain groups 
```

### Import
```bash
php artisan lang:import es.csv # localed autodetected from file name
php artisan lang:import espaniol.csv -l es
php artisan lang:import espaniol.csv -l es -g paggination,validation # import only cretain groups
php artisan lang:import es.csv -p # validate imported translations for missing placeholders (see below)
```

### Validate
```bash
php artisan lang:validate ar -m -v
```
![Laravel-Lang-Import-Export validation example](https://raw.githubusercontent.com/AidasK/laravel-lang-import-export/master/validation.png)


### Config

You can export package config if you want to set defaults for the commands:
```bash
php artisan vendor:publish
```


Changelog
------------

6.1.0
* Validate placeholders feature

6.0.0
* refactor whole repository

5.4.10
* Laravel 5.7 support

5.4.9
* Create new directory, when not exists before

5.4.8
* Fix UTF-8 encoding

5.4.7
*  Handling empty keys

5.4.6
* Laravel 5.6 support

5.4.3
- support Package Auto-Discovery

5.4.2
- resolve problems with PSR-4 autoloading

5.4.1
- improved import command
- improved Excel support
- support of [LaravelLocalization](https://github.com/mcamara/laravel-localization) routes files

5.4.0
- refactor whole repository
- add support for Excel
- add support for export and import all localization files
- any arguments are not required


Credits
------------

This package was originally created by [UFirst](http://github.com/ufirstgroup) and is available here: [Laravel-lang-import-export](https://github.com/ufirstgroup/laravel-lang-import-export).

Currently is developed by [Aidas Klimas](https://klimas.lt/), software house from Lithuania
