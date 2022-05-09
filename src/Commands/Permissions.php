<?php

namespace Silentz\Mailchimp\Commands;

use Illuminate\Console\Command;
use Spatie\Newsletter\NewsletterFacade;

class Permissions extends Command
{
    protected $signature = 'mailchimp:permissions {form}';

    protected $description = 'Get the marketing permissions for an audience';

    public function handle()
    {
        $this->table(
            ['Marketing Permission', 'ID'],
            NewsletterFacade::getMarketingPermissions($this->argument('form'))
        );
    }
}
