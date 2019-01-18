<?php
namespace ERP\Core\Documents\Persistables;

use ERP\Core\Documents\Properties\DocumentNamePropertyTrait;
use ERP\Core\Documents\Properties\DocumentUrlPropertyTrait;
use ERP\Core\Documents\Properties\DocumentSizePropertyTrait;
use ERP\Core\Documents\Properties\DocumentFormatPropertyTrait;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class DocumentPersistable
{
    use DocumentNamePropertyTrait;
    use DocumentUrlPropertyTrait;
    use DocumentSizePropertyTrait;
    use DocumentFormatPropertyTrait;
}