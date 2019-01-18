<?php
namespace ValuePad\Debug\Support;

use Ascope\Libraries\Permissions\PermissionsIgnorantInterface;
use Illuminate\Routing\Controller;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class BaseController extends Controller implements PermissionsIgnorantInterface
{

}