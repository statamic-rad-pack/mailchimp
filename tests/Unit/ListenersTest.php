<?php

namespace Edalzell\Mailchimp\Tests\Unit;

use Edalzell\Mailchimp\Listeners\AddFromSubmission;
use Edalzell\Mailchimp\Subscriber;
use Edalzell\Mailchimp\Tests\TestCase;
use Illuminate\Support\Facades\Event;
use Statamic\Facades\Form as FormAPI;
use Statamic\Fields\Blueprint;
use Statamic\Forms\Form as Form;
use Statamic\Forms\Submission;

class ListenersTest extends TestCase
{
    private Form $form;
    private Submission $submission;

    public function setUp(): void
    {
        parent::setUp();

        FormAPI::all()->each->delete();

        $blueprint = (new Blueprint)->setHandle('post')->save();

        $this->form = FormAPI::make('contact_us')
            ->title('Contact Us')
            ->blueprint($blueprint)
            ->honeypot('winnie');

        $this->form->save();

        $this->submission = $this->form->createSubmission();
        $this->submission
            ->unguard()
            ->data(['foo'=>'bar']);
    }


    /** @test */
    public function does_respond_to_events()
    {
        $this->mock(AddFromSubmission::class, function ($mock) {
            $mock->shouldReceive('handle')->once();
        });

        Event::dispatch('Form.submission.created', $this->submission);
    }
}
