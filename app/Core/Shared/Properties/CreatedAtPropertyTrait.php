<?php
namespace ERP\Core\Shared\Properties;

/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait CreatedAtPropertyTrait
{
    /**
     * @var int
     */
    private $created_at;

    /**
     * @param int $id
     */
    public function setCreated_at($created_at)
    {
        $this->created_at = $created_at;
    }

    /**
     * @return int
     */
    public function getCreated_at()
    {
        return $this->created_at;
    }
}