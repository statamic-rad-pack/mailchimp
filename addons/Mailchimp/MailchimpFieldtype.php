<?php

namespace Statamic\Addons\Mailchimp;

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
        return [
            'form' => null,
            'check_permission' => false,
            'permission_field' => null
        ];
    }

    /**
     * Pre-process the data before it gets sent to the publish page
     *
     * @param mixed $data
     * @return array|mixed
     */
    public function preProcess($data)
    {
        // Only have one of each field so it's stored as a simple string value.
        // However, the selectize field needs an array to convert to array
        $data['form'] = isset($data['form']) ? [$data['form']] : '';
        $data['permission_field'] = isset($data['permission_field']) ? [$data['permission_field']] : '';

        return $data;
    }

    /**
     * Process the data before it gets saved
     *
     * @param mixed $data
     * @return array|mixed
     */
    public function process($data)
    {
        // As the data comes from a selectize field, it's in an array.
        // We only have one of everything so get rid of all the arrays
        $data['form'] = isset($data['form']) ? reset($data['form']): '';
        $data['permission_field'] = isset($data['permission_field']) && !empty($data['permission_field'])
                                  ? $data['permission_field'][0]
                                  : '';

        return $data;
    }
}
