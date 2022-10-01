<?php

namespace Pivot;

use Exception;
use GuzzleHttp\Exception\GuzzleException;

/**
 * @author Zubs <zubairidrisaweda@gmail.com>
 * @package PivotPayPHP
 */
class PivotPay
{
    private string $merchantCode;
    private string $merchantSecret;
    private string $telecomCode;
    private string $apiUsername;
    private string $apiPassword;

    public function __construct(
        string $merchantCode,
        string $merchantSecret,
        string $apiUsername,
        string $apiPassword,
    )
    {
        $this->merchantCode = $merchantCode;
        $this->merchantSecret = $merchantSecret;
        $this->apiUsername = $apiUsername;
        $this->apiPassword = $apiPassword;
    }

    public function getTelecomCode(): string
    {
        return $this->telecomCode;
    }

    public function setTelecomCode(string $telecomCode): PivotPay
    {
        $this->telecomCode = $telecomCode;

        return $this;
    }

    /**
     * Validates payment reference. Verifies payment provider details before making payment.
     * @throws Exception|GuzzleException
     */
    public function validateReference
    (
        string $customerAccountNumber,
        string $customerType = null,
        string $customerCategory = null
    ): string
    {
        $processor = new ReferenceValidator($this->merchantCode, $this->apiPassword, $this->apiUsername, $this->merchantSecret);
        $processor->setCustomerAccountNumber($customerAccountNumber)
            ->setCustomerType($customerType)
            ->setCustomerCategory($customerCategory)
            ->setTelecomCode($this->getTelecomCode());

        return $processor->process();
    }
}
