<?php

namespace Pivot;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class ReferenceValidator extends Base
{
    private string $customerAccountNumber;
    private string | null $customerType = null;
    private string | null $customerCategory = null;
    private string $telecomCode;
    private string $merchantCode;
    private string $apiPassword;
    private string $apiUsername;
    private string $merchantSecret;

    public function __construct(string $merchantCode, string $apiPassword, string $apiUsername, string $merchantSecret)
    {
        $this->merchantCode = $merchantCode;
        $this->apiPassword = $apiPassword;
        $this->apiUsername = $apiUsername;
        $this->merchantSecret = $merchantSecret;
    }

    public function getCustomerAccountNumber(): string
    {
        return $this->customerAccountNumber;
    }

    public function setCustomerAccountNumber(string $customerAccountNumber): ReferenceValidator
    {
        $this->customerAccountNumber = $customerAccountNumber;

        return $this;
    }

    public function getCustomerType(): ?string
    {
        return $this->customerType;
    }

    public function setCustomerType(?string $customerType): ReferenceValidator
    {
        $this->customerType = $customerType;

        return $this;
    }

    public function getCustomerCategory(): ?string
    {
        return $this->customerCategory;
    }

    public function setCustomerCategory(?string $customerCategory): ReferenceValidator
    {
        $this->customerCategory = $customerCategory;

        return $this;
    }

    public function getTelecomCode(): string
    {
        return $this->telecomCode;
    }

    /**
     * @throws Exception
     */
    public function setTelecomCode(string $telecomCode): ReferenceValidator
    {
        switch (strtoupper($telecomCode)) {
            case Base::TELECOM_CODE_MTN:
            case Base::TELECOM_CODE_AIRTEL:
            case Base::TELECOM_CODE_MPESA:
                $this->telecomCode = $telecomCode;

                return $this;
            default:
                throw new Exception("Invalid telecom code");
        }
    }

    protected function getMerchantCode(): string
    {
        return $this->merchantCode;
    }

    protected function getRequestSignature(): string
    {
        $data = utf8_encode($this->getCustomerAccountNumber() . $this->getTelecomCode() . $this->getMerchantCode());
        $secret = utf8_encode($this->merchantSecret);
        $key = hash_hmac("sha512", $data, $secret, true);

        return base64_encode($key);
    }

    protected function getRequestPayload(): string
    {
        $payload = [
            'requestfield1' => $this->getCustomerAccountNumber(),
            'requestfield4' => $this->getTelecomCode(),
            'requestfield5' => $this->getMerchantCode(),
            'requestfield6' => $this->apiPassword,
            'requestfield7' => $this->getRequestSignature(),
        ];

        if ($this->getCustomerType()) {
            $payload['requestfield2'] = $this->getCustomerType();
        }

        if ($this->getCustomerCategory()) {
            $payload['requestfield3'] = $this->getCustomerCategory();
        }

        ksort($payload);

        return json_encode($payload);
    }

    /**
     * @throws Exception|GuzzleException
     */
    public function process(): string
    {
        $client = new Client([
            "base_uri" => Base::BASE_URL,
            "headers" => [
                "Authorization" => "Basic " . base64_encode($this->apiUsername . ":" . $this->apiPassword),
                "Accept" => "application/json",
                "Content-Type" => "application/json"
            ]
        ]);

        $request = $client->request("POST", Base::TRANS_VALIDATE_REFERENCE, [
            "body" => $this->getRequestPayload(),
        ]);

        return $request->getBody();
    }
}
