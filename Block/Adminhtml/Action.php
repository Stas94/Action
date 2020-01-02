<?php
namespace Puga\Action\Block\Adminhtml;

class Action extends \Magento\Backend\Block\Widget\Grid\Container
{

    protected function _construct()
    {
        $this->_controller = 'adminhtml_action';
        $this->_blockGroup = 'Puga_Action';
        $this->_headerText = __('Action');
        $this->_addButtonLabel = __('Create New Action');
        parent::_construct();
    }
}
