<?php

namespace Pivot;

abstract class Base
{
    const TELECOM_CODE_MTN = "MTN";
    const TELECOM_CODE_AIRTEL = "AIRTEL";
    const TELECOM_CODE_MPESA = "MPESA";
    const BASE_URL = "http://102.68.173.142:8001/merchantgateway/payments";
    const TRANS_VALIDATE_REFERENCE = "/validateReference";

    abstract protected function getMerchantCode(): string;

    abstract protected function getRequestPayload(): string;
}
