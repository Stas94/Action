<?php

namespace Puga\Action\Cron;

class Action
{

    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    protected $_localeResolver;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_localeDate;

    public function execute()
    {

        $this->_localeResolver->emulate(0);
        $currentDate = $this->_localeDate->date();

        return $this;

    }
}
