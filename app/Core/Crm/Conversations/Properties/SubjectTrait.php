<?php
namespace ERP\Core\Crm\Conversations\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait SubjectTrait
{
	/**
     * @var subject
     */
    private $subject;
	/**
	 * @param string $subject
	 */
	public function setSubject($subject)
	{
		$this->subject = $subject;
	}
	/**
	 * @return subject
	 */
	public function getSubject()
	{
		return $this->subject;
	}
}