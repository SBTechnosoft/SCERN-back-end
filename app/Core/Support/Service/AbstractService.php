<?php
namespace ERP\Core\Support\Service;

use ERP\Core\Shared\Interfaces\NotifierInterface;
use ERP\Core\Shared\Options\NotifierOptions;
use ERP\Core\Support\Service\ContainerInterface;
/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
abstract class AbstractService implements ContainerInterface
{
    /**
     * @var ContainerInterface
     * @var EntityManagerInterface
     */
    protected $container;
	protected $entityManager;
	
    public function __construct()
    {
	}
	/**
	 * @param object $notification
	 * @param NotifierOptions $options
	 */
	protected function notify($notification, NotifierOptions $options = null)
	{
		/**
		 * @var NotifierInterface $notifier
		 */
		$notifier = $this->container->get(NotifierInterface::class);

		$notifier->notify($notification, $options);
	}
    /**
     *
     * @param object $src
     * @param object $dest
     * @param array $config
     * @return object
     */
    protected function transfer($src, $dest, array $config = [])
    {
        $transferer = new Transferer($config);
        return $transferer->transfer($src, $dest);
    }
} 