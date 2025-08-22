<?php

namespace StatamicRadPack\Mailchimp\Tests\Unit;

use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\Test;
use Statamic\Events\SubmissionCreated;
use Statamic\Events\UserRegistered;
use Statamic\Facades\Form as FormAPI;
use Statamic\Forms\Form;
use Statamic\Forms\Submission;
use StatamicRadPack\Mailchimp\Listeners\AddFromSubmission;
use StatamicRadPack\Mailchimp\Listeners\AddFromUser;
use StatamicRadPack\Mailchimp\Tests\TestCase;

class ListenersTest extends TestCase
{
    private Form $form;

    private Submission $submission;

    protected function setUp(): void
    {
        parent::setUp();

        FormAPI::all()->each->delete();

        $this->form = FormAPI::make('contact_us')
            ->title('Contact Us')
            ->honeypot('winnie');

        $this->form->save();

        $this->submission = $this->form->makeSubmission();
        $this->submission
            ->data(['foo' => 'bar']);
    }

    #[Test]
    public function does_respond_to_submission_created_event()
    {
        $event = new SubmissionCreated($this->submission);

        $this->mock(AddFromSubmission::class)->shouldReceive('handle')->with($event)->once();

        Event::dispatch($event);
    }

    #[Test]
    public function does_respond_to_user_registered_event()
    {
        $event = new UserRegistered([]);

        $this->mock(AddFromUser::class)->shouldReceive('handle')->with($event)->once();

        Event::dispatch($event);
    }
}
