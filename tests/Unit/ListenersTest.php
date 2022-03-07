<?php

namespace Silentz\Mailchimp\Tests\Unit;

use Illuminate\Support\Facades\Event;
use Silentz\Mailchimp\Listeners\AddFromSubmission;
use Silentz\Mailchimp\Listeners\AddFromUser;
use Silentz\Mailchimp\Tests\TestCase;
use Statamic\Events\SubmissionCreated;
use Statamic\Events\UserRegistered;
use Statamic\Facades\Form as FormAPI;
use Statamic\Forms\Form;
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
    public function does_respond_to_submission_created_event()
    {
        $event = new SubmissionCreated($this->submission);

        $this->mock(AddFromSubmission::class)->shouldReceive('handle')->with($event)->once();

        Event::dispatch($event);
    }

    /** @test */
    public function does_respond_to_user_registered_event()
    {
        $event = new UserRegistered([]);

        $this->mock(AddFromUser::class)->shouldReceive('handle')->with($event)->once();

        Event::dispatch($event);
    }
}
