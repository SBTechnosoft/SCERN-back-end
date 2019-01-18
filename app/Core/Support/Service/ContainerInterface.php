<?php
namespace ERP\Core\Support\Service;

/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
interface ContainerInterface
{
    /**
     *
     * @param string $abstract
     * @param string|object|array $callerOrParameters
     * @param array $parameters
     * @return object
     */ 
    public function get($id,$name);
    /**
     *
     * @param callable $method
     * @return mixed
     */
    public function invoke(callable $method);
} 