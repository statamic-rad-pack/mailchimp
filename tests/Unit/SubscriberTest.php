<?php

namespace Edalzell\Mailchimp\Tests\Unit;

use Edalzell\Mailchimp\Subscriber;
use Edalzell\Mailchimp\Tests\TestCase;
use Statamic\Facades\Form as FormAPI;
use Statamic\Fields\Blueprint;
use Statamic\Forms\Form as Form;
use Statamic\Forms\Submission;

class SubscriberTest extends TestCase
{
    private Form $form;

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
    }


    /** @test */
    public function can_create_subscriber_from_submission()
    {
        $submission = $this->form->createSubmission();
        $submission
            ->unguard()
            ->data(['foo'=>'bar']);

        $subscriber = Subscriber::createFromSubmission($submission);

        $this->assertInstanceOf(Subscriber::class, $subscriber);
    }
}
