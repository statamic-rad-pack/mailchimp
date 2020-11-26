<?php

namespace Silentz\Mailchimp\Commands;

use Illuminate\Console\Command;
use Spatie\Newsletter\NewsletterFacade;

class GetInterests extends Command
{
    protected $signature = 'mailchimp:interests {form} {group}';

    protected $description = 'Get the group interests';

    public function handle()
    {
        $mailchimp = NewsletterFacade::getApi();

        $config = collect(config('mailchimp.forms', []))->firstWhere('form', $this->argument('form'));

        $response = NewsletterFacade::getApi()->get("lists/{$config['audience_id']}/interest-categories/{$this->argument('group')}/interests");

        $this->line('');

        $this->info("Group {$this->argument('group')}:");

        $headers = ['Interest', 'ID'];

        $data = collect($response['interests'])->map(fn ($interest) => [$interest['name'], $interest['id']]);

        $this->table($headers, $data);
    }
}
