<?php
namespace SR\DeliveryDate\Model\Adminhtml\System\Config\Source;

class DeliveryTime implements \Magento\Framework\Option\ArrayInterface
{
    public function __construct(\Magento\Customer\Model\ResourceModel\Group\CollectionFactory $groupCollectionFactory)
    {
        $this->_groupCollectionFactory = $groupCollectionFactory;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
      
        $deleveryTime = [
            '01:00AM - 02:00AM',
            '07:00AM - 09:00AM',
            '09:00AM - 11:00AM',
            '11:00AM - 01:00PM',
            '01:00PM - 03:00PM',
            '03:00PM - 06:00PM',
            '06:00PM - 08:00PM',
            '08:00PM - 10:00PM',
            '10:00PM - 11:30PM',
        ];

foreach($deleveryTime as $data){
    $this->_options[] = ['label' => $data, 'value' => $data];
}
       
        return $this->_options;
    }
}