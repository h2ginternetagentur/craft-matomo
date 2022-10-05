<?php

namespace h2g\matomo;

use Craft;
use craft\events\RegisterComponentTypesEvent;
use craft\services\Dashboard;
use h2g\matomo\models\Settings;
use h2g\matomo\services\MatomoMetadataService;
use h2g\matomo\widgets\MatomoClient;
use h2g\matomo\widgets\MatomoWidget;
use yii\base\Event;

class Matomo extends \craft\base\Plugin
{
    // Public Properties
    // =========================================================================

    /**
     * @var bool
     */
    public bool $hasCpSettings = true;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
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

    // Protected Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    protected function createSettingsModel(): Settings
    {
        return new \h2g\matomo\models\Settings();
    }

    /**
     * @inheritdoc
     */
    protected function settingsHtml(): string
    {
        return Craft::$app->view->renderTemplate(
            'matomo/settings',
            [
                'settings' => $this->getSettings(),
                ...self::getInstance()->matomoMetadataService->getWidgetsMetadata(),
            ]
        );
    }
}
