<?php

namespace h2g\matomo;

use Craft;
use craft\base\Plugin;
use craft\events\RegisterComponentTypesEvent;
use craft\services\Dashboard;
use h2g\matomo\models\Settings;
use h2g\matomo\services\MatomoMetadataService;
use h2g\matomo\widgets\MatomoClient;
use h2g\matomo\widgets\MatomoWidget;
use yii\base\Event;


class Matomo extends Plugin
{

    public bool $hasCpSettings = true;

    public function init()
    {
        parent::init();

        $this->setComponents([
            'matomoMetadataService' => MatomoMetadataService::class,
        ]);

        Event::on(
            Dashboard::class,
            Dashboard::EVENT_REGISTER_WIDGET_TYPES,
            function (RegisterComponentTypesEvent $event) {
                  $event->types[] = MatomoWidget::class;
            }
        );
    }


    protected function createSettingsModel(): Settings
    {
        return new Settings();
    }

    protected function settingsHtml(): string
    {
        return Craft::$app->view->renderTemplate(
            'matomo/settings',
            [
                'settings' => $this->getSettings(),
                ...MatomoMetadataService::instance()->getWidgetsMetadata()
            ]
        );
    }
}
