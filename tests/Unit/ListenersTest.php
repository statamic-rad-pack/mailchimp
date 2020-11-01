<?php

namespace Silentz\Mailchimp\Tests\Unit;

use Silentz\Mailchimp\Listeners\AddFromSubmission;
use Silentz\Mailchimp\Tests\TestCase;
use Illuminate\Support\Facades\Event;
use Statamic\Events\SubmissionCreated;
use Statamic\Facades\Form as FormAPI;
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

        $this->form = FormAPI::make('contact_us')
            ->title('Contact Us')
            ->honeypot('winnie');

        $this->form->save();

        $this->submission = $this->form->makeSubmission();
        $this->submission
            ->data(['foo'=>'bar']);
    }

    /** @test */
    public function does_respond_to_events()
    {
        $event = new SubmissionCreated($this->submission);

        $this->mock(AddFromSubmission::class)->shouldReceive('handle')->with($event)->once();

        Event::dispatch($event);
    }
}
