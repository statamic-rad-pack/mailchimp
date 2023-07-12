<?php

namespace StatamicRadPack\Mailchimp\Commands;

use Illuminate\Console\Command;
use Spatie\Newsletter\Facades\Newsletter;

class Permissions extends Command
{
    protected $signature = 'mailchimp:permissions {form}';

    protected $description = 'Get the marketing permissions for an audience';

    public function handle()
    {
        $this->table(
            ['Marketing Permission', 'ID'],
            Newsletter::getMarketingPermissions($this->argument('form'))
        );
    }
}
