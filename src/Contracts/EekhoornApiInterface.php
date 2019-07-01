<?php

namespace Eekhoorn\PhpSdk\Contracts;

use Eekhoorn\PhpSdkInterface\Contracts\EekhoornApiInterface as BaseEekhoornApiInterface;

interface EekhoornApiInterface extends BaseEekhoornApiInterface, JsonApiSdkInterface, ParsesJsonApiInterface
{
}
