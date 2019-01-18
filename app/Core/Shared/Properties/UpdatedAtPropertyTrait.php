<?php
namespace ERP\Core\Shared\Properties;

/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait UpdatedAtPropertyTrait
{
    /**
     * @var int
     */
    private $updated_at;

    /**
     * @param int $id
     */
    public function setUpdated_at($updated_at)
    {
        $this->updated_at = $updated_at;
    }

    /**
     * @return int
     */
    public function getUpdated_at()
    {
        return $this->updated_at;
    }
}