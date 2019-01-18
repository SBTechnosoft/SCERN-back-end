<?php
namespace ERP\Core\Settings\MeasurementUnits\Persistables;

use ERP\Core\Shared\Properties\NamePropertyTrait;
use ERP\Core\Settings\MeasurementUnits\Properties\MeasurementUnitIdTrait;
use ERP\Core\Settings\MeasurementUnits\Properties\UnitNameTrait;
use ERP\Core\Shared\Properties\KeyPropertyTrait;
/**
 * @author Farhan Shaikh<farhan.s@siliconbrain.in>
 */
class MeasurementPersistable
{
    use NamePropertyTrait;
    use KeyPropertyTrait;
    use MeasurementUnitIdTrait;
    use UnitNameTrait;
}