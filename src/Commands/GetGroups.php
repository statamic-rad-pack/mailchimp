<?php

namespace StatamicRadPack\Mailchimp\Commands;

use Illuminate\Console\Command;
use Statamic\Facades\Form;
use StatamicRadPack\Mailchimp\Facades\Newsletter;

class GetGroups extends Command
{
    protected $signature = 'mailchimp:groups {form}';

    protected $description = "Get form's groups";

    public function handle()
    {
        if (! $config = Form::find($this->argument('form'))?->get('mailchimp.settings', [])) {
            $this->error('Form not found');

            return;
        }

        $response = Newsletter::getApi()->get("lists/{$config['audience_id']}/interest-categories");

        $this->line('');

        $this->info("Form {$this->argument('form')}:");

        $headers = ['Group', 'ID'];

        $data = collect($response['categories'])->map(fn ($group) => [$group['title'], $group['id']]);

        $this->table($headers, $data);
    }
}
