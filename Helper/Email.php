<?php
/**
 * @author QuangChien(Glorious Victory) <quangchien01.it@gmail.com>
 * @copyright Copyright © 2023 QuangChien(Glorious Victory) <https://www.facebook.com/quangchien01>. All rights reserved.
 */

namespace Victory\DeleteOrders\Helper;

use Exception;
use Magento\Framework\App\Area;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class Email
{
    /**
     * Email config path
     */
    const EMAIL_CONFIGURATION = '/email';

    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param TransportBuilder $transportBuilder
     * @param Data $helperData
     * @param LoggerInterface $logger
     */
    public function __construct(
        TransportBuilder $transportBuilder,
        Data $helperData,
        LoggerInterface $logger
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->helperData = $helperData;
        $this->logger = $logger;
    }

    /**
     * @param array $templateParams
     * @param null $storeId
     *
     * @return $this
     */
    public function sendEmailTemplate($templateParams = [], $storeId = null)
    {
        try {
            $toEmails = $this->getToEmail($storeId);
            foreach ($toEmails as $toEmail) {
                $transport = $this->transportBuilder
                    ->setTemplateIdentifier($this->getTemplate($storeId))
                    ->setTemplateOptions(['area' => Area::AREA_FRONTEND, 'store' => $storeId])
                    ->setTemplateVars($templateParams)
                    ->setFrom($this->getSender($storeId))
                    ->addTo($toEmail)
                    ->getTransport();

                $transport->sendMessage();
            }
        } catch (Exception $e) {
            $this->logger->critical($e->getMessage());
        }

        return $this;
    }

    /**
     * @param $code
     * @param $storeId
     * @return mixed
     */
    public function getConfigEmail($code = '', $storeId = null)
    {
        $code = ($code !== '') ? '/' . $code : '';

        return $this->helperData->getConfigValue(Data::MODULE_CONFIG_PATH . self::EMAIL_CONFIGURATION . $code, $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return bool
     */
    public function isEnabledEmail($storeId = null)
    {
        if ($this->helperData->isEnabled()) {
            return (bool) $this->getConfigEmail('enabled', $storeId);
        }

        return false;
    }

    /**
     * @param null $storeId
     *
     * @return string
     */
    public function getSender($storeId = null)
    {
        return $this->getConfigEmail('sender', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return string
     */
    public function getTemplate($storeId = null)
    {
        return $this->getConfigEmail('template', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return array
     */
    public function getToEmail($storeId = null)
    {
        return explode(',', $this->getConfigEmail('to', $storeId) ?? "");
    }
}
