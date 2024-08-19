<?php

namespace StatamicRadPack\Mailchimp\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use Statamic\Facades\Form;
use StatamicRadPack\Mailchimp\Migrators\ConfigToFormData;
use StatamicRadPack\Mailchimp\Tests\TestCase;

class MigratorTest extends TestCase
{
    #[Test]
    public function it_migrates_config_data_to_the_form()
    {
        Form::make('test')
            ->handle('test')
            ->data([])
            ->save();

        (new ConfigToFormData)
            ->handle([
                'form' => 'test',
                'id' => 'OJ1gCdil',
                'check_consent' => true,
                'consent_field' => 'consent',
                'disable_opt_in' => false,
                'interests_field' => 'interests',
                'marketing_permissions_field' => 'gdpr',
                'marketing_permissions_field_ids' => [],
                'merge_fields' => [
                    [
                        'id' => 'KFC3e5jw',
                    ],
                ],
                'primary_email_field' => 'email',
            ]);

        $data = Form::find('test')->data()->all();
        
        $this->assertArrayHasKey('mailchimp', $data);
        
        $this->assertSame($data['mailchimp'], [
            'enabled' => true,
            'settings' => [
                'check_consent' => true,
                'consent_field' => 'consent',
                'disable_opt_in' => false,
                'interests_field' => 'interests',
                'marketing_permissions_field' => 'gdpr',
                'marketing_permissions_field_ids' => [],
                'merge_fields' => [
                    [
                        'id' => 'KFC3e5jw',
                    ],
                ],
                'primary_email_field' => 'email',
            ],
        ]);
    }
}
