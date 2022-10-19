<?php

namespace h2g\matomo\widgets;

use Craft;
use craft\base\Widget;
use h2g\matomo\Matomo;

class MatomoWidget extends Widget
{

    public string $parameters = '';

    public static function displayName(): string
    {
        return Craft::t('matomo', 'Matomo Widget');
    }


    public static function icon(): string
    {
        return Craft::getAlias("@h2g/matomo/assets/svgs/widgets/matomo.svg");
    }

    public static function maxColspan(): ?int
    {
        return null;
    }


    public function rules(): array
    {
        $rules = parent::rules();
        $rules = array_merge(
            $rules,
            [
                ['parameters', 'string'],
                ['parameters', 'required']
            ]
        );
        return $rules;
    }

    /**
     * @inheritDoc
     */
    public function getSettingsHtml(): string
    {
        ['categories' => $categories, 'groupedWidgets' => $groupedWidgets] =
            Matomo::getInstance()->matomoMetadataService->getWidgetsMetadata();

        $availableWidgets = [];
        foreach ($categories as $categoryId => $category) {
            $availableWidgets[] = ['category' => $category];
            $availableWidgets = array_merge($availableWidgets, $groupedWidgets[$categoryId]);
        }


        return Craft::$app->getView()->renderTemplate(
            'matomo/matomoWidgetSettings',
            [
                'widget' => $this,
                'availableWidgets' => $availableWidgets,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public
    function getBodyHtml(): ?string
    {
        Craft::$app->getView()->registerAssetBundle(MatomoWidgetAsset::class);

        return Craft::$app->getView()->renderTemplate(
            'matomo/matomoWidget',
            [
                'widgetUrl' => $this->getWidgetUrl()
            ]
        );
    }

    private function getWidgetUrl(): string
    {
        $settings = Matomo::getInstance()->settings;

        $parameters = [
            'module' => 'Widgetize',
            'action' => 'iframe',
            'disabledLink' => 1,
            'widget' => 1,
            'idSite' => $settings->siteId,
            'period' => 'day',
            'date' => 'today',
            'language' => substr(Craft::$app->locale, 0, 2),
            'token_auth' => $settings->authToken,
        ];

        $widgetParameters = json_decode($this->parameters, true);
        $widgetParameters['moduleToWidgetize'] = $widgetParameters['module'];
        $widgetParameters['actionToWidgetize'] = $widgetParameters['action'];
        unset($widgetParameters['module'], $widgetParameters['action']);

        $parameters = array_merge($parameters, $widgetParameters);
    }
}
