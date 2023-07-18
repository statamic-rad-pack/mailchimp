<?php

namespace StatamicRadPack\Mailchimp\Commands;

use Illuminate\Console\Command;
use StatamicRadPack\Mailchimp\Facades\Newsletter;

class GetGroups extends Command
{
    protected $signature = 'mailchimp:groups {form}';

    protected $description = "Get forms' groups";

    public function handle()
    {
        $config = collect(config('mailchimp.forms', []))->firstWhere('form', $this->argument('form'));

        $response = Newsletter::getApi()->get("lists/{$config['audience_id']}/interest-categories");

        $this->line('');

        $this->info("Form {$config['form']}:");

        $headers = ['Group', 'ID'];

        $data = collect($response['categories'])->map(fn ($group) => [$group['title'], $group['id']]);

        $this->table($headers, $data);
    }
}
