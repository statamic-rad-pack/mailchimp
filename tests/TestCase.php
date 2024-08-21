<?php

namespace StatamicRadPack\Mailchimp\Tests;

use JMac\Testing\Traits\AdditionalAssertions;
use Statamic\Facades\Blueprint as BlueprintFacade;
use Statamic\Facades\YAML;
use Statamic\Statamic;
use Statamic\Testing\AddonTestCase;
use Statamic\Testing\Concerns\PreventsSavingStacheItemsToDisk;
use StatamicRadPack\Mailchimp\ServiceProvider;

class TestCase extends AddonTestCase
{
    use AdditionalAssertions, PreventsSavingStacheItemsToDisk;

    protected string $addonServiceProvider = ServiceProvider::class;

    protected function getPackageProviders($app)
    {
        return array_merge(parent::getPackageProviders($app), [\Stillat\Proteus\WriterServiceProvider::class]);
    }

    protected function resolveApplicationConfiguration($app)
    {
        parent::resolveApplicationConfiguration($app);

        // Assume the pro edition within tests
        $app['config']->set('statamic.editions.pro', true);

        Statamic::booted(function () {
            $blueprintContents = YAML::parse(file_get_contents(__DIR__.'/__fixtures__/blueprints/contact_us.yaml'));
            $blueprintFields = collect($blueprintContents['sections']['main']['fields'])
                ->keyBy(fn ($item) => $item['handle'])
                ->map(fn ($item) => $item['field'])
                ->all();

            BlueprintFacade::makeFromFields($blueprintFields)
                ->setNamespace('forms.contact_us')
                ->setHandle('contact_us')
                ->save();
        });
    }
}
