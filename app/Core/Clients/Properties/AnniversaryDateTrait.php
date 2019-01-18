<?php
namespace ERP\Core\Clients\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait AnniversaryDateTrait
{
	/**
     * @var anniversaryDate
     */
    private $anniversaryDate;
	/**
	 * @param string $anniversaryDate
	 */
	public function setAnniversaryDate($anniversaryDate)
	{
		$this->anniversaryDate = $anniversaryDate;
	}
	/**
	 * @return anniversaryDate
	 */
	public function getAnniversaryDate()
	{
		return $this->anniversaryDate;
	}
}