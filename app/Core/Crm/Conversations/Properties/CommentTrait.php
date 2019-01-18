<?php
namespace ERP\Core\Crm\Conversations\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait CommentTrait
{
	/**
     * @var comment
     */
    private $comment;
	/**
	 * @param string $comment
	 */
	public function setComment($comment)
	{
		$this->comment = $comment;
	}
	/**
	 * @return comment
	 */
	public function getComment()
	{
		return $this->comment;
	}
}