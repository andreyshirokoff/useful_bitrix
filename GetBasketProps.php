<?php

namespace App;

use Bitrix\Sale,
    Bitrix\Main;
class GetBasketProps
{
    /**
     * Запиши коды свойств элементов из корзины в массив, которые хочешь вывести
     */
    protected static function codeProperty()
    {
        return array(
            "ED_DLINA",
            "ED_SHIRINA",
            "ED_VYSOTA"
        );
    }

    /**
     * Получем массив ID элементов в корзине
     */
    public static function getBasketElementID(): array
    {
        $basket = Sale\Basket::loadItemsForFUser(Bitrix\Sale\Fuser::getId(), Main\Context::getCurrent()->getSite());
        $getBasket = $basket->getBasket();
        $elementIDs = [];
        foreach($getBasket as $basketItem)
        {
            $product = $basketItem->getFieldValues();
            array_push($elementIDs, $product["PRODUCT_ID"]);
        }

        return $elementIDs;
    }

    /**
     * Получаем ID инфоблока элементов в козине
     */
    public static function getBasketIBlockID()
    {
        $IBLOCK_ID_OBJ = CIBlockElement::GetList(
            array(),
            array("ID"=>self::getBasketElementID()[0]),
            false,
            false,
            array("IBLOCK_ID")
        );
        $IBLOCK_ID = [];
        while($IBLOCK_ID_RES = $IBLOCK_ID_OBJ->Fetch())
            $IBLOCK_ID = $IBLOCK_ID_RES["IBLOCK_ID"];

        return $IBLOCK_ID;
    }

    /**
     * Получаем массив cвойств элементов в корзине и их значения
     */
    public static function getBasketElementPropertyValue()
    {
        $propsValue = [];
        foreach (self::getBasketElementID() as $elementID) {
            $props = [];
            foreach (self::codeProperty() as $key)
            {
                $propObj = CIBlockElement::GetProperty(
                    self::getBasketIBlockID(),
                    $elementID,
                    array(),
                    array("CODE" => $key)
                );
                $value = [];
                if($propRes = $propObj->Fetch())
                    $value = $propRes["VALUE"];
                $props[$key] = $value;
            }
            $propsValue[$elementID] = $props;
        }

        return $propsValue;
    }
}
