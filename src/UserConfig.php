<?php

namespace StatamicRadPack\Mailchimp;

use Illuminate\Support\Collection;
use Statamic\Facades\Blink;
use Statamic\Facades\File;
use Statamic\Facades\YAML;

class UserConfig extends Collection
{
    private array $config;

    /**
     * Load user config defaults collection.
     *
     * @param  array|Collection|null  $config
     */
    public function __construct($config = null)
    {
        if (! is_null($config)) {
            $config = collect($config)->all();
        }

        $this->config = $config ?? $this->getSavedSettings();
    }

    /**
     * Load user config collection.
     *
     * @param  array|Collection|null  $config
     * @return static
     */
    public static function load($config = null)
    {
        $class = app(UserConfig::class);

        return new $class($config);
    }

    /**
     * Save user config to yaml.
     */
    public function config()
    {
        return $this->config;
    }

    /**
     * Does it exist yet?
     */
    public function exists()
    {
        return File::exists($this->path());
    }

    /**
     * Save user config to yaml.
     */
    public function save()
    {
        File::put($this->path(), YAML::dump($this->config));
    }

    /**
     * Get user config from yaml.
     *
     * @return array
     */
    protected function getSavedSettings()
    {
        return Blink::once('statamic-mailchimp::user-config', function () {
            return collect(YAML::file($this->path())->parse())
                ->all() ?? [];
        });
    }

    /**
     * Get site defaults yaml path.
     *
     * @return string
     */
    protected function path()
    {
        return resource_path('mailchimp.yaml');
    }
}
