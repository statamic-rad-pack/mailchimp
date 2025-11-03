<?php

namespace StatamicRadPack\Mailchimp;

use Illuminate\Support\Collection;
use Statamic\Addons\Settings;
use StatamicRadPack\Mailchimp\Exceptions\InvalidNewsletterList;

class NewsletterListCollection extends Collection
{
    /** @var string */
    public $defaultListName = '';

    public static function createFromSettings(Settings $settings): self
    {
        $collection = new static;

        $lists = collect($settings->get('lists', []))
            ->map(fn ($listProperties, $name) => new NewsletterList($name, $listProperties))
            ->all();

        $collection->collect($lists);
        $collection->defaultListName = $settings->get('default_list_name', 'subscribers');

        return $collection;
    }

    public function findByName(string $name): NewsletterList
    {
        if ($name === '') {
            return $this->getDefault();
        }

        foreach ($this->items as $newsletterList) {
            if ($newsletterList->getName() === $name) {
                return $newsletterList;
            }
        }

        throw InvalidNewsletterList::noListWithName($name);
    }

    public function getDefault(): NewsletterList
    {
        foreach ($this->items as $newsletterList) {
            if ($newsletterList->getName() === $this->defaultListName) {
                return $newsletterList;
            }
        }

        throw InvalidNewsletterList::defaultListDoesNotExist($this->defaultListName);
    }
}
