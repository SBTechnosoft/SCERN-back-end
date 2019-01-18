<?php
namespace ERP\Core\Settings\Persistables;

use ERP\Core\Shared\Properties\NamePropertyTrait;
use ERP\Core\Shared\Properties\KeyPropertyTrait;
use ERP\Core\Settings\Properties\BarcodeWidthPropertyTrait;
use ERP\Core\Settings\Properties\BarcodeHeightPropertyTrait;
use ERP\Core\Settings\Properties\ChequenoStatusTrait;
use ERP\Core\Settings\Properties\ServiceDateNoOfDaysTrait;
use ERP\Core\Settings\Properties\PaymentdateNoOfDaysTrait;
use ERP\Core\Settings\Properties\PaymentdateStatusTrait;

use ERP\Core\Settings\Properties\BirthreminderTypeTrait;
use ERP\Core\Settings\Properties\BirthreminderTimeTrait;
use ERP\Core\Settings\Properties\BirthreminderNotifyByTrait;
use ERP\Core\Settings\Properties\BirthreminderStatusTrait;

use ERP\Core\Settings\Properties\AnnireminderTypeTrait;
use ERP\Core\Settings\Properties\AnnireminderTimeTrait;
use ERP\Core\Settings\Properties\AnnireminderNotifyByTrait;
use ERP\Core\Settings\Properties\AnnireminderStatusTrait;

use ERP\Core\Settings\Properties\ProductColorStatusTrait;
use ERP\Core\Settings\Properties\ProductSizeStatusTrait;
use ERP\Core\Settings\Properties\ProductBestBeforeStatusTrait;
use ERP\Core\Settings\Properties\ProductFrameNoStatusTrait;
use ERP\Core\Settings\Properties\ProductAdvanceMouStatusTrait;
use ERP\Core\Settings\Properties\ProductMrpRequireStatusTrait;
use ERP\Core\Settings\Properties\ProductMarginStatusTrait;

use ERP\Core\Settings\Properties\ClientWorkNoStatusTrait;
use ERP\Core\Settings\Properties\ClientAddressStatusTrait;
use ERP\Core\Settings\Properties\ClientEmailIdStatusTrait;
use ERP\Core\Settings\Properties\ClientStateStatusTrait;
use ERP\Core\Settings\Properties\ClientCityStatusTrait;
use ERP\Core\Settings\Properties\ClientProfessionStatusTrait;

use ERP\Core\Settings\Properties\BillSalesmanStatusTrait;

use ERP\Core\Settings\Properties\AdvanceSalesStatusTrait;
use ERP\Core\Settings\Properties\AdvancePurchaseStatusTrait;

use ERP\Core\Settings\Properties\WebintegrationStatusTrait;
use ERP\Core\Settings\Properties\WebintegrationUserIdTrait;
use ERP\Core\Settings\Properties\WebintegrationPasswordTrait;
use ERP\Core\Settings\Properties\WebintegrationPushUrlTrait;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class SettingPersistable
{
    use BillSalesmanStatusTrait;
    
    use ClientWorkNoStatusTrait;
    use ClientAddressStatusTrait;
    use ClientEmailIdStatusTrait;
    use ClientStateStatusTrait;
    use ClientCityStatusTrait;
    use ClientProfessionStatusTrait;
    
    use NamePropertyTrait;
	use KeyPropertyTrait;
    use BarcodeWidthPropertyTrait;
    use BarcodeHeightPropertyTrait;
    use ChequenoStatusTrait;
    use ServiceDateNoOfDaysTrait;
    use PaymentdateNoOfDaysTrait;
    use PaymentdateStatusTrait;

    use BirthreminderTypeTrait;
    use BirthreminderTimeTrait;
    use BirthreminderNotifyByTrait;
    use BirthreminderStatusTrait;

    use AnnireminderTypeTrait;
    use AnnireminderTimeTrait;
    use AnnireminderNotifyByTrait;
    use AnnireminderStatusTrait;
    
    use ProductColorStatusTrait;
    use ProductSizeStatusTrait;
    use ProductBestBeforeStatusTrait;
    use ProductFrameNoStatusTrait;
    use ProductAdvanceMouStatusTrait;
    use ProductMrpRequireStatusTrait;
    use ProductMarginStatusTrait;

    use AdvanceSalesStatusTrait;
    use AdvancePurchaseStatusTrait;

    use WebintegrationStatusTrait;
    use WebintegrationUserIdTrait;
    use WebintegrationPasswordTrait;
    use WebintegrationPushUrlTrait;
}