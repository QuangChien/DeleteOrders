<?php
/**
 * @author QuangChien(Glorious Victory) <quangchien01.it@gmail.com>
 * @copyright Copyright © 2023 QuangChien(Glorious Victory) <https://www.facebook.com/quangchien01>. All rights reserved.
 */

namespace Victory\DeleteOrders\Ui\Component\Listing\Columns;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Victory\DeleteOrders\Helper\Data;
use Magento\Backend\Model\Widget\Grid\Row\UrlGeneratorFactory;

class DeletedBy extends Column
{
    /**
     * delete manually
     */
    const DELETED_BY_MANUALLY = 'Manually';

    /**
     * delete auto
     */
    const DELETED_BY_AUTO = 'Automation';

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param Data $helperData
     * @param UrlGeneratorFactory $urlGeneratorFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     * @throws LocalizedException
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['entity_id'])) {
                    $deletedBy = $item['delete_type'] == 1 ? __(self::DELETED_BY_MANUALLY) : ($item['delete_type'] == 2 ? __(self::DELETED_BY_AUTO) : '');
                    $item[$this->getData('name')] = $deletedBy;
                }
            }
        }
        return $dataSource;
    }
}
