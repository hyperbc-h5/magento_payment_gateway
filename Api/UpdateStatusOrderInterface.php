<?php

declare(strict_types=1);

namespace Hyperbc\PaymentGateway\Api;

interface UpdateStatusOrderInterface
{
    /**
     * Postback Hyperbc
     * @return string
     */
    public function doUpdate();
}
