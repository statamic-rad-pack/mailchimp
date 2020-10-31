<?php

namespace Silentz\Mailchimp\Concerns;

use Statamic\Facades\Blueprint as BlueprintAPI;
use Statamic\Facades\Path;
use Statamic\Facades\YAML;
use Statamic\Fields\Blueprint;

trait HasBlueprint
{
    private function getBlueprint(): Blueprint
    {
        // @TODO gotta be a better way to do this
        $addonPath = Path::tidy(__DIR__.'/../../');
        $path = Path::assemble($addonPath, 'resources', 'blueprints', 'config.yaml');

        return BlueprintAPI::makeFromFields(YAML::file($path)->parse());
    }
}
