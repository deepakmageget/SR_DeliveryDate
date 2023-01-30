<?php
namespace SR\DeliveryDate\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class DeliveryDateConfigProvider implements ConfigProviderInterface
{
    const XPATH_FORMAT = 'carriers/deliverydateandtime/format';
    const XPATH_DISABLED = 'carriers/deliverydateandtime/disabled';
    const XPATH_ACTIVE = 'carriers/deliverydateandtime/active';
    const XPATH_HOURMIN = 'carriers/deliverydateandtime/hourMin';
    const XPATH_HOURMAX = 'carriers/deliverydateandtime/hourMax';

    const XPATH_STARTDATE = 'carriers/deliverydateandtime/startdate';
    const XPATH_ENDDATE = 'carriers/deliverydateandtime/enddate';
    const XPATH_CUTOFMIN = 'carriers/deliverydateandtime/cutofminute';

    const XPATH_OPTIONDELIVERYTIME = 'carriers/deliverydateandtime/optiondeliverytime';
    const XPATH_START_TIME = 'carriers/deliverydateandtime/starttimne';
    const XPATH_END_TIME = 'carriers/deliverydateandtime/endtimne';


    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    protected $timezoneInterface;
    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        TimezoneInterface $timezoneInterface
    ) {
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->timezoneInterface = $timezoneInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $store = $this->getStoreId();
        $disabled = $this->scopeConfig->getValue(self::XPATH_DISABLED, ScopeInterface::SCOPE_STORE, $store);
        $active = $this->scopeConfig->getValue(self::XPATH_ACTIVE, ScopeInterface::SCOPE_STORE, $store);
        $hourMin = $this->scopeConfig->getValue(self::XPATH_HOURMIN, ScopeInterface::SCOPE_STORE, $store);
        $hourMax = $this->scopeConfig->getValue(self::XPATH_HOURMAX, ScopeInterface::SCOPE_STORE, $store);
        $format = $this->scopeConfig->getValue(self::XPATH_FORMAT, ScopeInterface::SCOPE_STORE, $store);

        $startDate = $this->scopeConfig->getValue(self::XPATH_STARTDATE, ScopeInterface::SCOPE_STORE, $store);
        $endDate = $this->scopeConfig->getValue(self::XPATH_ENDDATE, ScopeInterface::SCOPE_STORE, $store);
        $cutofmin = $this->scopeConfig->getValue(self::XPATH_CUTOFMIN, ScopeInterface::SCOPE_STORE, $store);

        $optionDeliveryTime = $this->scopeConfig->getValue(self::XPATH_OPTIONDELIVERYTIME, ScopeInterface::SCOPE_STORE, $store);
        $starttime = $this->scopeConfig->getValue(self::XPATH_START_TIME, ScopeInterface::SCOPE_STORE, $store);
        $endtime = $this->scopeConfig->getValue(self::XPATH_END_TIME, ScopeInterface::SCOPE_STORE, $store);
        
        $deliveryTimeOptions = (explode(",", $optionDeliveryTime)); 

        $noday = 0;
        if($disabled == -1) {
            $noday = 1;
        }

            $deleveryTimeArray = ['Select Time'];

            $getStoreTime = $this->getStoreTime(); // 164902 // 052127
           
          

          //for Current date

        foreach($deliveryTimeOptions as $optionTime){

            $configtime1 = $optionTime;
            $timeCheckAmPm = (explode("-", $configtime1)); 

            $timeAm = strrpos($timeCheckAmPm[0],"AM");
            if($timeAm){
                $time1 = (explode(":", $configtime1)); // Array ( [0] => 07 [1] => 00AM - 10 [2] => 00AM )
                $time1 = $time1[0]; //07
                $updateTime1 = $time1.$cutofmin.'00'; // 073000
                if($updateTime1 >= $getStoreTime){
                    $deleveryTimeArray[] =  $configtime1;
                }
            }
            elseif(strrpos($timeCheckAmPm[0],"PM")){
                $time1 = (explode(":", $configtime1)); 
                $time1 = $time1[0]; //07
                $time1 = (int)$time1+ (int)12; //15
                $updateTime1 = $time1.$cutofmin.'00'; // 073000
                if($updateTime1 >= $getStoreTime){
                    $deleveryTimeArray[] =  $configtime1;
                }
            }

        }



        //for other date
        $otherdeleveryTimeArray = ['Select Time'];
        foreach($deliveryTimeOptions as $optionTime){
            $otherdeleveryTimeArray[] =  $optionTime;
        }
        // for startdate 
        if(isset($starttime) && !empty($starttime)){
            $starttimnenew = "";
            $starttimearr = (explode(",", $starttime)); 
            foreach($starttimearr as $starttimnenewvalue){
                $starttimnenew.=$starttimnenewvalue;
    
            }

            $endtimnenew = "";
            $endtimearr = (explode(",", $endtime)); 
            if(isset($endtime) && !empty($endtime)){
                foreach($endtimearr as $endtimnenewvalue){
                    $endtimnenew.=$endtimnenewvalue;
        
                }
            }

            if($starttimnenew >= $getStoreTime){
                $deleveryTimeArray=[];
                $starttimeinoptions =  str_replace(",",":",$starttime);
                $deleveryTimeArray []= "Todays we are available after - ". $starttimeinoptions;
                // if($startDate == 'today'){
                //     $startDate = '+1d';
                // }
               
            }
            if($endtimnenew <= $getStoreTime){
                $deleveryTimeArray=[];
                $starttimeinoptions =  str_replace(",",":",$endtime);
                $deleveryTimeArray []= "todays end time - ". $starttimeinoptions;
                if($startDate == 'today'){
                    $startDate = '+1d';
                }
            }
        }
      
        $configData['customvalue']['customdata'] = ['test1','test2','test2'];
        $config = [
            'shipping' => [
                'delivery_date' => [
                    'format' => $format,
                    'disabled' => $disabled,
                    'active' => $active,
                    'noday' => $noday,
                    'hourMin' => $hourMin,
                    'hourMax' => $hourMax,
                    'startDate' => $startDate,
                    'endDate' => $endDate,
                    'cutofmin' => $cutofmin,
                    'starttimne' => $starttimnenew,
                ],
                'delivery_time' => [
                    'customvalue'=>$deleveryTimeArray,
                    
                ],
                'otherdelevery_time'=>[
                    'customtime'=>$otherdeleveryTimeArray,
                ]
            ]
        ];

        return $config;
    }

    public function getStoreId()
    {
        return $this->storeManager->getStore()->getStoreId();
    }

    public function getStoreTime()
    {
        $formatDate = $this->timezoneInterface->formatDate();
        // you can also get format wise date and time
        $dateTime = $this->timezoneInterface->date()->format('Y-m-d H:i:s');
        $date = $this->timezoneInterface->date()->format('Y-m-d');
        $time = $this->timezoneInterface->date()->format('His');
        return $time;
    }
}