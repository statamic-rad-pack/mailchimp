<?php

namespace Statamic\Addons\Mailchimp\Fieldtypes;

use Statamic\Extend\Fieldtype;

class MailchimpFieldtype extends Fieldtype
{
    /**
     * The blank/default value
     *
     * @return array
     */
    public function blank()
    {
        return [];
    }

    /**
     * Pre-process the data before it gets sent to the publish page
     *
     * @param mixed $data
     * @return array
     */
    public function preProcess($data)
    {
        // Only have one of each field so it's stored as a simple string value.
        // However, the selectize field needs an array, so convert to array
        return isset($data) ? [$data] : [];
    }

    /**
     * Process the data before it gets saved
     *
     * @param mixed $data
     * @return array
     */
    public function process($data)
    {
        // As the data comes from a selectize field, it's in an array.
        // We only have one of everything so get rid of all the arrays
        return isset($data) && !empty($data) ? $data[0] : [];
    }
}
