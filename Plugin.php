<?php namespace ImbaSynergy\Integrationwidget;

use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function registerComponents()
    {
        return [
        	'ImbaSynergy\integrationwidget\Components\ImbaChat' => 'ImbaChat'
        ];
    }

    public function pluginDetails()
    {
        return [
            'name'        => 'IntegrationImbachat',
            'description' => 'ImbaChat integration'
        ];
    }
}
