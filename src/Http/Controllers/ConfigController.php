<?php

namespace StatamicRadPack\Mailchimp\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Statamic\Facades\Blueprint;
use Statamic\Facades\YAML;
use Statamic\Fields\Blueprint as BlueprintContract;
use Statamic\Http\Controllers\CP\CpController;
use StatamicRadpack\Mailchimp\UserConfig;

class ConfigController extends CpController
{
    public function edit()
    {
        abort_if(Auth::user()->cant('manage mailchimp settings'), 403);

        $values = config('mailchimp');
        $values['users'] = [$values['users']];

        $blueprint = $this->getBlueprint();

        $fields = $blueprint->fields()->addValues($values)->preProcess();

        return view('mailchimp::cp.config', [
            'blueprint' => $blueprint->toPublishArray(),
            'values' => $fields->values(),
            'meta' => $fields->meta(),
        ]);
    }

    public function update(Request $request)
    {
        abort_if(Auth::user()->cant('manage mailchimp settings'), 403);

        $fields = $this->getBlueprint()->fields()->addValues($request->all());

        $fields->validate();

        $values = $fields->process()->values()->all();
        $values['users'] = $values['users'][0];

        UserConfig::load(Arr::only($values, ['add_new_users', 'users']))->save();

        return response()->json(['message' => __('Settings updated')]);
    }

    private function getBlueprint(): BlueprintContract
    {
        return Blueprint::make()->setContents(YAML::file(__DIR__.'/../../../resources/blueprints/config.yaml')->parse());
    }
}
