<?php

namespace StatamicRadPack\Mailchimp\Commands;

use Illuminate\Console\Command;
use Statamic\Facades\Form;
use StatamicRadPack\Mailchimp\Facades\Newsletter;

class GetInterests extends Command
{
    protected $signature = 'mailchimp:interests {form} {group}';

    protected $description = 'Get the group interests';

    public function handle()
    {
        $mailchimp = Newsletter::getApi();

        if (! $config = Form::find($this->argument('form'))?->get('mailchimp.settings', [])) {
            $this->error('Form not found');

            return;
        }

        $response = $mailchimp->get("lists/{$config['audience_id']}/interest-categories/{$this->argument('group')}/interests");

        $this->line('');

        $this->info("Group {$this->argument('group')}:");

        $headers = ['Interest', 'ID'];

        $data = collect($response['interests'])->map(fn ($interest) => [$interest['name'], $interest['id']]);

        $this->table($headers, $data);
    }
}
