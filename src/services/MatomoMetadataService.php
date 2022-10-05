<?php

namespace h2g\matomo\services;

use craft\base\Component;
use GraphQL\Type\Definition\Type;
use h2g\matomo\widgets\MatomoClient;
use yii\base\ErrorException;
use yii\db\Exception;

class MatomoMetadataService extends Component
{
    // Private Properties
    // =========================================================================

    /**
     * @var MatomoClient
     */
    private MatomoClient $matomoClient;

    // Constructor
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function __construct($config = [])
    {
        parent::__construct($config);

        $this->matomoClient = new MatomoClient();
    }

    // Public Methods
    // =========================================================================

    /*
     * @return mixed
     */
    public function getWidgetsMetadata(): array
    {
        $data = $this->matomoClient->getWithParams([
            'module' => 'API',
            'method' => 'API.getWidgetMetadata',
        ]);

        if ($data == [] || array_key_exists('result', $data) && $data['result'] == 'error') return $data;

        // Grouping
        $categories = [];
        $groupedWidgets = [];
        foreach ($data as $widget) {
            $categoryId = $widget['category']['id'];
            $categoryId = is_numeric($categoryId)? "ID$categoryId" : $categoryId;
            if (!isset($categories[$categoryId])) $categories[$categoryId] = $widget['category'];

            if ($widget['subcategory'] != null) {
                $subcategoryId = $widget['subcategory']['id'];
                $subcategoryId = is_numeric($subcategoryId)? "ID$subcategoryId" : $subcategoryId;
                if (!isset($categories[$categoryId]['subcategories'][$subcategoryId]))
                    $categories[$categoryId]['subcategories'][$subcategoryId] = $widget['subcategory'];

                $groupedWidgets[$categoryId][$subcategoryId][] = $widget;
            } else {
                $groupedWidgets[$categoryId][] = $widget;
            }
        }

        // Sorting
        uasort($categories, fn($a, $b) => $a['order'] <=> $b['order']);
        foreach($categories as &$category) {
            if (isset($category['subcategories'])) {
                uasort($category['subcategories'], fn($a, $b) => $a['order'] <=> $b['order']);
            }
        }
        uksort($groupedWidgets, fn($a, $b) => $categories[$a]['order'] <=> $categories[$b]['order']);
        foreach ($groupedWidgets as $categoryId => &$group) {
            uksort($group, function ($a, $b) use ($group, $categoryId, $categories) {
                $a = is_int($a) ? $group[$a]['order'] : $categories[$categoryId]['subcategories'][$a]['order'];
                $b = is_int($b) ? $group[$b]['order'] : $categories[$categoryId]['subcategories'][$b]['order'];
                return $a <=> $b;
            });
            foreach ($group as $key => &$value) {
                if (is_string($key)) uasort($value, fn($a, $b) => $a['order'] <=> $b['order']);
            }
        }

        return [
            'categories' => $categories,
            'groupedWidgets' => $groupedWidgets,
        ];
    }
}