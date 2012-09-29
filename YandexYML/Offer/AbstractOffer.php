<?php

namespace Jeka\YandexYMLBundle\YandexYML\Offer;

abstract class AbstractOffer
{

    private $_id;
    /**
     * Статус доступности товара - в наличии/на заказ
     * available="false" - товарное предложение на заказ. Магазин готов осуществить поставку товара на указанных условиях в течение месяца (срок может быть больше для товаров, которые всеми участниками рынка поставляются только на заказ).. Те товарные предложения, на которые заказы не принимаются, не должны выгружаться в Яндекс.Маркет.
     * available="true" - товарное предложение в наличии. Магазин готов сразу договариваться с покупателем о доставке товара
     * Более точное описание можно посмотреть в требованиях к рекламным Материалам.
     * @var boolean
     */
    public $available;

    /**
     * URL-адрес страницы товара
     * @var string
     */
    public $url = '';

    /**
     * Цена, по которой данный товар можно приобрести.Цена товарного предложения округляеся и выводится в зависимости от настроек пользователя.
     * @var float
     */
    public $price = 0;
    /**
     * Идентификатор валюты товара (RUR, USD, UAH, KZT). Для корректного отображения цены в национальной валюте, необходимо использовать идентификатор (например, UAH) с соответствующим значением цены.
     * @var string
     */
    public $currencyId = 'RUR';

    /**
     * Идентификатор категории товара (целое число не более 18 знаков). Товарное предложение может принадлежать только одной категории
     * categoryId+
     * @var int
     */
    public $categoryId = 0;

    /**
     * Ссылка на картинку соответствующего товарного предложения. Недопустимо давать ссылку на "заглушку", т.е. на картинку где написано "картинка отсутствует" или на логотип магазина
     * @var string
     */
    public $picture = null;
    /**
     * Наименование товарного предложения
     * @var string
     */
    public $name = null;

    /**
     * Элемент, предназначенный для того, чтобы показать пользователям, чем отличается данный товар от других, или для описания акций магазина (кроме скидок). Допустимая длина текста в элементе - 50 символов.
     * @var string
     */
    public $sales_notes = null;


    /**
     * Элемент, обозначающий возможность доставить соответствующий товар. "false" данный товар не может быть доставлен("самовывоз").
     * "true" товар доставляется на условиях, которые указываются в партнерском интерфейсе http://partner.market.yandex.ru на странице "редактирование".
     * @var boolean
     */
    public $delivery = false;

    /**
     * Стоимость доставки данного товара в Своем регионе
     * @var float
     */
    public $local_delivery_cost = null;
    /**
     * Элемент предназначен для указания страны производства товара.
     * @var string
     */
    public $country_of_origin = null;

    /**
     * Группа товаров \ категория
     * @var string
     */
    public $typePrefix = null;
    /**
     *
     * @var string
     */
    public $description = '';


    function __construct($id, $available)
    {
        $this->id        = $id;
        $this->available = $available;
    }

    abstract function getType();

    /**
     * для организации правильного порядка
     * возвращает свойства класса в нужном порядке
     * для формирования xml
     * @abstract
     * @return array
     */
    abstract function getOrderArray();


    public function toXML()
    {
        $xml  = new DOMDocument('1.0');
        $root = $xml->appendChild($xml->createElement('offer'));

        if ($this->id) {
            $root->appendChild(new DOMAttr('id', $this->id));
        }

        $root->appendChild(new DOMAttr('available', $this->available ? 'true' : 'false'));

        if ($this->getType()) {
            $root->appendChild(new DOMAttr('type', $this->getType()));
        }

        foreach ($this->getOrderArray() as $field) {
            if (!isset($this->$field)) {
                continue;
            }

            $key   = $field;
            $value = $this->$field;

            if (!is_null($value)) {
                if (is_bool($value)) {
                    $value = $value ? 'true' : 'false';
                } else {
                    $value = htmlspecialchars($value);
                }

                $root->appendChild(new DOMElement($key, $value));
            }
        }

        return $xml;
    }
}


