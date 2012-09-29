<?php

namespace Jeka\YandexYMLBundle\YandexYML;

use Jeka\YandexYMLBundle\YandexYML\Offer\AbstractOffer;
use DOMImplementation;
use DOMAttr;
use DOMElement;

class Document
{
    /**
     * @var \DOMDocument
     */
    private $xml = null;
    /**
     * @var \DOMElement
     */
    private $xshop = null;
    /**
     * @var \DOMElement
     */
    private $xymlCatalog = null;

    private $shopCompany = '';
    private $shopName = '';
    private $shopUrl = '';

    private $localDeliveryCost = null;
    private $deliveryIncluded = null;

    private $allowedCurrencies = array('RUR', 'RUB', 'USD', 'EUR', 'UAH', 'KZT');

    private $offers = array();

    private $currencies = array(
        'RUR'=> 1,
        'USD'=> 'CBRF',
        'EUR'=> 'CBRF',
        'UAH'=> 'CBRF',
        'KZT'=> 'CBRF'
    );

    private $categories = array();


    /**
     *
     * @param string $shopName
     * @param string $shopCompany
     * @param string $shopUrl
     * @param array $options eq array(
     *      'currencies'            => array('USD'=>'29.2',...),
     *      'categories'            => array(...)
     *      'local_delivery_cost'   => 300
     *      'delivery_included'     => false
     *  );
     */
    public function __construct($shopName, $shopCompany, $shopUrl, array $options = array())
    {

        $this->shopCompany = $shopCompany;
        $this->shopName    = $shopName;
        $this->shopUrl     = $shopUrl;

        if (isset($options['currencies'])) {
            $this->setCurrencies($options['currencies']);
        }

        if (isset($options['categories'])) {
            $this->setCategories($options['categories']);
        }

        if (isset ($options['local_delivery_cost'])) {
            $this->setLocalDeliveryCost($options['local_delivery_cost']);
        }
        if (isset ($options['delivery_included'])) {
            $this->setDeliveryIncluded($options['delivery_included']);
        }

        $this->initXML();
    }

    private function setCategories(array $categories)
    {
        $this->categories = $categories;
    }

    public function setLocalDeliveryCost($value)
    {
        $this->localDeliveryCost = $value;

        return $this;
    }

    public function setDeliveryIncluded($value)
    {
        $this->deliveryIncluded = (boolean)$value;

        return $this;
    }


    public function setOffers(array $offers)
    {
        $this->offers = $offers;
    }

    public function addOffer(AbstractOffer $offer)
    {
        $this->offers[] = $offer;
    }

    public function generateYML()
    {

        $xshop = $this->xshop;

        $xcurrencies = $xshop->appendChild(new DOMElement('currencies'));
        foreach ($this->currencies as $id=> $rate) {
            $xcurr = $xcurrencies->appendChild(new DOMElement('currency'));
            $xcurr->appendChild(new DOMAttr('id', $id));
            $xcurr->appendChild(new DOMAttr('rate', $rate));
        }

        $xcategories = $xshop->appendChild(new DOMElement('categories'));
        foreach ($this->categories as $cat) {
            $xcategories->appendChild($this->xml->importNode($cat->toXML()->documentElement, true));
        }

        $xoffers = $xshop->appendChild(new DOMElement('offers'));
        foreach ($this->offers as $offer) {
            $xoffers->appendChild($this->xml->importNode($offer->toXML()->documentElement, true));
        }
    }

    /**
     *
     * @return string
     */
    public function saveYML()
    {

        return $this->xml->saveXML();
    }

    private function initXML()
    {
        $imp = new DOMImplementation();
        $dtd = $imp->createDocumentType('yml_catalog', '', "shops.dtd");

        $xml               = $imp->createDocument('', '', $dtd);
        $xml->encoding     = 'utf-8';
        $xml->formatOutput = true;

        $yml_catalog = $xml->appendChild(new DOMElement('yml_catalog'));
        $yml_catalog->appendChild(new DOMAttr('date', date('Y-m-d H:i:s')));

        $shop = $yml_catalog->appendChild(new DOMElement('shop'));

        $this->xymlCatalog = $yml_catalog;
        $this->xshop       = $shop;
        $this->xml         = $xml;

        $this->xshop->appendChild(new DOMElement('name', htmlspecialchars($this->shopName)));
        $this->xshop->appendChild(new DOMElement('company', htmlspecialchars($this->shopCompany)));
        $this->xshop->appendChild(new DOMElement('url', htmlspecialchars($this->shopUrl)));
    }


    public function addCategory(Category $category)
    {
        $this->categories[] = $category;

        return $this;
    }


    public function setCurrencies(array $currencies)
    {
        $this->currencies = $currencies;

        return $this;
    }

    public function setCurrency($currency_code, $rate)
    {
        $this->currencies[$currency_code] = $rate;

        return $this;
    }

    /**
     * @return DOMDocument
     */
    public function getXML()
    {
        return $this->xml;
    }
}