<?php

namespace h2g\matomo\widgets;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class MatomoWidgetAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritDoc
     */
    public function init()
    {
        $this->sourcePath = "@h2g/matomo/assets";

        $this->depends = [
            CpAsset::class
        ];

        $this->js = [
            'js/iframeResizer.min.js',
            'js/widgets/resizeIframe.min.js',
        ];

        $this->css = [
            'css/widgets/matomoWidget.min.css',
        ];

        parent::init();
    }
}