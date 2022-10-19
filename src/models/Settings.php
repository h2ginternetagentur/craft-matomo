<?php

namespace h2g\matomo\models;

use craft\base\Model;
use h2g\matomo\Matomo;

class Settings extends Model
{

    public ?string $matomoUrl = null;
    public ?int $siteId = null;
    public ?string $authToken = null;

    public function defineRules(): array
    {
        return [
            [['matomoUrl', 'siteId', 'authToken'], 'required'],
            ['matomoUrl', 'url', 'defaultScheme' => 'https'],
            ['siteId', 'integer', 'min' => 0],
            ['authToken', 'string'],
            ['authToken', 'connectionValidator']
        ];
    }

    public function connectionValidator($attribute, $params)
    {
        $data = Matomo::getInstance()->matomoMetadataService->getWidgetsMetadata();
        if ($data == [] || array_key_exists('result', $data) && $data['result'] == 'error') {
            foreach (['matomoUrl', 'siteId', 'authToken'] as $attribute) {
                $this->addError($attribute, 'Login credentials were not accepted');
            }
        }
    }
}
