<?php

namespace Omnipay\SagePay\Message;

/**
 * Sage Pay Direct Purchase Request
 */
class DirectPurchaseRequest extends DirectAuthorizeRequest
{
    protected $action = 'REPEAT';

    public function getData()
    {
        $this->validate('amount', 'card', 'transactionId', 'transactionRef');

        $data = $this->getBaseData();
        unset($data['AccountType']); // TODO - check this is really needed

        $data['VendorTxCode'] = $this->getTransactionId();
        $data['Amount'] = $this->getAmount();
        $data['Currency'] = $this->getCurrency();
        $data['Description'] = $this->getDescription();
        $data['RelatedVPSTxId'] = $this->getRelatedVPSTxId();
        $data['RelatedVendorTxCode'] = $this->getRelatedTransactionId();
        $data['RelatedSecurityKey'] = $this->getRelatedSecurityKey();
        $data['RelatedTxAuthNo'] = $this->getRelatedTxAuthNo();
        $data['CV2'] = $this->getCard()->getCvv();

        // shipping details
        if ($this->anyCardDeliveryDataSpecified()) {
            $card = $this->getCard();
            $data['DeliverySurname'] = $card->getShippingLastName();
            $data['DeliveryFirstnames'] = $card->getShippingFirstName();
            $data['DeliveryAddress1'] = $card->getShippingAddress1();
            $data['DeliveryAddress2'] = $card->getShippingAddress2();
            $data['DeliveryCity'] = $card->getShippingCity();
            $data['DeliveryPostCode'] = $card->getShippingPostcode();
            $data['DeliveryCountry'] = $card->getShippingCountry();
            $data['DeliveryState'] = $card->getShippingCountry() === 'US' ? $card->getShippingState() : '';
            $data['DeliveryPhone'] = $card->getShippingPhone();
        }

        return $data;
    }

    protected function getRelatedVPSTxId() {
        $relatedRef = json_decode($this->getTransactionReference(), true);
        return $relatedRef['VPSTxId'];
    }

    protected function getRelatedTransactionId() {
        $relatedRef = json_decode($this->getTransactionReference(), true);
        return $relatedRef['VendorTxCode'];
    }

    protected function getRelatedSecurityKey() {
        $relatedRef = json_decode($this->getTransactionReference(), true);
        return $relatedRef['SecurityKey'];
    }

    protected function getRelatedTxAuthNo() {
        $relatedRef = json_decode($this->getTransactionReference(), true);
        return $relatedRef['TxAuthNo'];
    }

    protected function anyCardDeliveryDataSpecified() {
        $card = $this->getCard();

        return false; // TODO - check if there is delivery data in the card
    }

}
