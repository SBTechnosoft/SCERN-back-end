<?php
namespace ERP\Core\Shared\Properties;

/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait KeyPropertyTrait
{
    /**
     * @var keyName
     */
    private $keyName;

    /**
     * @param $keyName
     */
    public function setKey($keyName)
    {
        $this->keyName = $keyName;
    }

    /**
     * @return
     */
    public function getKey()
    {
        return $this->keyName;
    }
}