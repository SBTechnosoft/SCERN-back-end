<?php
namespace ERP\Support\Interfaces;

use Illuminate\Contracts\Routing\Registrar as RegistrarInterface;
/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
interface RouteRegistrarInterface
{
    /**
     * @return string
     */
    public function register(RegistrarInterface $registrar);
}