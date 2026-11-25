<?php

namespace StatamicRadPack\Mailchimp\Tests\Unit;

use Facades\Statamic\Fields\FieldtypeRepository;
use PHPUnit\Framework\Attributes\Test;
use Statamic\Fieldtypes\FormFields;
use StatamicRadPack\Mailchimp\Fieldtypes\FormFields as MailchimpFormFields;
use StatamicRadPack\Mailchimp\Tests\TestCase;

class FieldtypesTest extends TestCase
{
    #[Test]
    public function does_not_override_the_core_form_fields_fieldtype()
    {
        $this->assertInstanceOf(FormFields::class, FieldtypeRepository::find('form_fields'));
        $this->assertInstanceOf(MailchimpFormFields::class, FieldtypeRepository::find('mailchimp_form_fields'));
    }
}
