<?php

namespace Edalzell\Mailchimp\Http\Controllers\Config;

use Edalzell\Mailchimp\Concerns\HasBlueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Statamic\Http\Controllers\Controller;
use Stillat\Proteus\Support\Facades\ConfigWriter;

class UpdateConfigController extends Controller
{
    use HasBlueprint;

    public function __invoke(Request $request)
    {
        $blueprint = $this->getBlueprint();

        // Get a Fields object, and populate it with the submitted values.
        $fields = $blueprint->fields()->addValues($request->all());

        // Perform validation. Like Laravel's standard validation, if it fails,
        // a 422 response will be sent back with all the validation errors.
        $fields->validate();

        ConfigWriter::writeMany('mailchimp', $this->postProcess($fields->process()->values()->toArray()));
    }

    private function postProcess(array $values): array
    {
        return $values;
        // return Arr::set(
        //     $values,
        //     'forms',
        //     collect(Arr::get($values, 'forms'))
        //         ->mapWithKeys(fn ($data) => [$data['handle'] => $data])
        //         ->all()
        // );
    }
}
