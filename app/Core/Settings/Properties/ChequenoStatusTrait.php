<?php
namespace ERP\Core\Settings\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait ChequenoStatusTrait
{
	/**
     * @var chequeno
     */
    private $chequeno;
	/**
	 * @param string $chequeno
	 */
	public function setChequenoStatus($chequeno)
	{
		$this->chequeno = $chequeno;
	}
	/**
	 * @return chequeno
	 */
	public function getChequenoStatus()
	{
		return $this->chequeno;
	}
}