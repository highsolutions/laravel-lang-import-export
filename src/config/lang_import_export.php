<?php
return [
    /**
     * In practice export_locale value is 'en' and export_target is a list of all locales supported by your website
     */
    'base_locale' => 'en',
    'export_locale' => 'en',// locale list separated by comma
    'export_target' => null,// target locale list separated by comma
    'base_group' => null, // all groups or comma separated list
    'export_path' => storage_path(':locale:target.csv'),
];
