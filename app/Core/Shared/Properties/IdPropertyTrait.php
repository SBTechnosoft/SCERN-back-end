<?php
namespace ERP\Core\Shared\Properties;

/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait IdPropertyTrait
{
    /**
     * @var int
     */
    private $id;

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}