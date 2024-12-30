<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.txt
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to a newer
 * versions in the future. If you wish to customize this module for your needs
 * please refer to CustomizationPolicy.txt file inside our module for more information.
 *
 * @author Webkul IN
 * @copyright Since 2010 Webkul
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class WkMpSellerShipping extends ObjectModel
{
    public $id_wk_mp_shipping;
    public $id_seller;
    public $id_ps_reference;
    public $is_default_shipping;
    public $date_add;
    public $date_upd;

    public static $definition = [
        'table' => 'wk_mp_seller_shipping',
        'primary' => 'id_wk_mp_shipping',
        'fields' => [
            'id_seller' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true],
            'id_ps_reference' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true],
            'is_default_shipping' => ['type' => self::TYPE_BOOL],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDateFormat'],
            'date_upd' => ['type' => self::TYPE_DATE, 'validate' => 'isDateFormat'],
        ],
    ];

    public function deleteMpShipping($idMpShipping)
    {
        $objMpShipping = new self($idMpShipping);
        $idPsReference = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue(
            'SELECT `id_ps_reference` FROM `' . _DB_PREFIX_ . 'wk_mp_seller_shipping` WHERE `id_wk_mp_shipping` = ' . (int) $idMpShipping
        );
        if ($idPsReference) {
            // Delete shipping impact price
            Db::getInstance()->delete('wk_mp_seller_shipping_impact', 'mp_shipping_id = ' . (int) $idMpShipping);

            $del = Db::getInstance()->update('carrier', ['deleted' => 1], 'id_reference = ' . $idPsReference);
            if ($del) {
                $objMpShipping->delete();
            }
        } else {
            $objMpShipping->delete();
        }

        // unlink logo from marketplace directory
        if (file_exists(_PS_MODULE_DIR_ . 'marketplace/views/img/mpshipping/' . $idMpShipping . '.jpg')) {
            unlink(_PS_MODULE_DIR_ . 'marketplace/views/img/mpshipping/' . $idMpShipping . '.jpg');
        }
    }

    public static function getOnlyPrestaCarriers($idLang)
    {
        $carrDetailsFinal = Carrier::getCarriers($idLang, true, false, false, null, ALL_CARRIERS);
        if (!$carrDetailsFinal) {
            return false;
        }

        $onlyPsCarriers = [];
        if ($carrDetailsFinal) {
            foreach ($carrDetailsFinal as $carrVal) {
                $mpCarrier = Db::getInstance()->getRow('SELECT `id_wk_mp_shipping` FROM `' . _DB_PREFIX_ . 'wk_mp_seller_shipping` WHERE `id_ps_reference` = ' . (int) $carrVal['id_reference']);

                if (empty($mpCarrier)) {
                    $onlyPsCarriers[] = $carrVal;
                }
            }
        }

        return $onlyPsCarriers;
    }

    public static function getReferenceByMpShippingId($mpShippingId)
    {
        $idPsReference = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue(
            'SELECT `id_ps_reference` FROM `' . _DB_PREFIX_ . 'wk_mp_seller_shipping` WHERE `id_wk_mp_shipping` = ' . (int) $mpShippingId
        );

        if (empty($idPsReference)) {
            return false;
        } else {
            return $idPsReference;
        }
    }

    public static function getMpShippingId($idPsReference)
    {
        $mpShippingId = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue(
            'SELECT `id_wk_mp_shipping` AS `id` FROM `' . _DB_PREFIX_ . 'wk_mp_seller_shipping`
            WHERE `id_ps_reference` = ' . (int) $idPsReference
        );
        if (empty($mpShippingId)) {
            return false;
        } else {
            return (int) $mpShippingId;
        }
    }

    public function isSellerShippingByIdReference($psIdRef)
    {
        $isSellerShipping = Db::getInstance()->getRow('SELECT `id_wk_mp_shipping` FROM `' . _DB_PREFIX_ . 'wk_mp_seller_shipping` WHERE `id_ps_reference` = ' . (int) $psIdRef);
        if ($isSellerShipping) {
            return true;
        }

        return false;
    }

    public function updateCarriersOnDeactivateOrDelete()
    {
        $adminDefShipping = Configuration::get('MP_SHIPPING_ADMIN_DEFAULT');
        /* Assign new selected shipping methods to the seller produccts which have no seller shipping methods */
        $allSellerAdminProducts = Db::getInstance()->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'wk_mp_seller_product` WHERE `id_ps_product`!= 0');
        if ($allSellerAdminProducts) {
            $objMpShpMethod = new self();
            foreach ($allSellerAdminProducts as $valProd) {
                $prodObj = new Product($valProd['id_ps_product']);
                $carriersLst = $prodObj->getCarriers();
                $toChange = 1;
                $toAssign = 1;
                foreach ($carriersLst as $valCrr) {
                    $isSellerCrr = $objMpShpMethod->isSellerShippingByIdReference($valCrr['id_reference']);
                    if ($isSellerCrr) {
                        $toChange = 0;
                        if ($valCrr['active'] == 1) {
                            $toAssign = 0;
                        }
                    }
                }
                if ($toChange || $toAssign) {
                    // set carrier using carrier reference
                    $prodObj->setCarriers(json_decode($adminDefShipping));
                }
            }
        }
        /* END */
    }

    public function getAllReferenceId()
    {
        $idPsReference = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT `id_ps_reference` FROM `' . _DB_PREFIX_ . 'wk_mp_seller_shipping`');

        if (empty($idPsReference)) {
            return false;
        } else {
            return $idPsReference;
        }
    }

    public static function getCarrierIdByReference($idPsReference)
    {
        $idCarrier = Db::getInstance()->getValue('SELECT `id_carrier` FROM `' . _DB_PREFIX_ . 'carrier`
			WHERE `id_reference` = ' . (int) $idPsReference . ' AND deleted = 0 ORDER BY id_carrier DESC');

        if (empty($idCarrier)) {
            return false;
        } else {
            return $idCarrier;
        }
    }

    public function getMpShippingMethods($mpIdSeller, $idShop = false)
    {
        if (!$idShop) {
            $idShop = (int) Context::getContext()->shop->id;
        }

        $mpShippingData = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT *, `id_wk_mp_shipping` AS `id` FROM `' . _DB_PREFIX_ . 'wk_mp_seller_shipping` ms
            INNER JOIN `' . _DB_PREFIX_ . 'carrier` c ON (ms.`id_ps_reference`=c.`id_reference`)
            INNER JOIN `' . _DB_PREFIX_ . 'carrier_shop` cs ON (cs.`id_carrier`=c.`id_carrier`)
            WHERE `id_seller` = ' . (int) $mpIdSeller . '
            AND cs.`id_shop` = ' . (int) $idShop . '
            AND c.`deleted` = 0 AND c.`active` = 1 GROUP BY ms.`id_ps_reference`'
        );
        if ($mpShippingData) {
            return $mpShippingData;
        }

        return false;
    }

    public function getDefaultMpShippingMethods($mpIdSeller)
    {
        $mpShippingData = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT *, `id_wk_mp_shipping` AS `id` FROM `' . _DB_PREFIX_ . 'wk_mp_seller_shipping` ms
            INNER JOIN `' . _DB_PREFIX_ . 'carrier` c ON (ms.`id_ps_reference`=c.`id_reference`)
            WHERE `id_seller` = ' . (int) $mpIdSeller .
            ' AND `deleted` = 0 AND `active` = 1 AND `is_default_shipping` = 1'
        );

        if (empty($mpShippingData)) {
            return false;
        } else {
            return $mpShippingData;
        }
    }

    public function getAllShippingMethodNotDelete($mpIdSeller, $delete)
    {
        $mpShippingDetail = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT *, `id_wk_mp_shipping` AS `id` FROM `' . _DB_PREFIX_ . 'wk_mp_seller_shipping` ms
            INNER JOIN `' . _DB_PREFIX_ . 'carrier` c ON (ms.`id_ps_reference`=c.`id_reference`)
            INNER JOIN `' . _DB_PREFIX_ . 'carrier_shop` cs ON (cs.`id_carrier`=c.`id_carrier`)
            WHERE `id_seller` = ' . (int) $mpIdSeller . '
            AND c.`deleted` = ' . (int) $delete . ' GROUP BY ms.`id_ps_reference`'
        );

        if (empty($mpShippingDetail)) {
            return false;
        } else {
            return $mpShippingDetail;
        }
    }

    public function updateRange($objCarrier, $rangeInf, $rangeSup, $zoneFees, $mpShippingId = false)
    {
        $zoneDetail = Zone::getZones(false, true);
        $rangeType = (int) $objCarrier->shipping_method;
        if ($table = $objCarrier->getRangeTable()) {
            $objCarrier->deleteDeliveryPrice($table);
        }
        if (!empty($rangeInf) && !$objCarrier->is_free) {
            foreach ($rangeInf as $key => $value) {
                if ($rangeInf[$key] != '') {
                    if ($rangeSup[$key] == '') {
                        $delimiter2 = (float) 0;
                    } else {
                        $delimiter2 = (float) $rangeSup[$key];
                    }
                    if (isset($objCarrier->shipping_method) && $rangeType == Carrier::SHIPPING_METHOD_WEIGHT) {
                        $rangeObj = new RangeWeight();
                    } else {
                        $rangeObj = new RangePrice();
                    }
                    $rangeObj->id_carrier = (int) $objCarrier->id;
                    $rangeObj->delimiter1 = (float) $value;
                    $rangeObj->delimiter2 = (float) $delimiter2;
                    $rangeObj->save();

                    if ($mpShippingId && $rangeObj->id) {
                        $getImpactPriceArr = WkMpSellerShippingImpact::getAllImpactPriceByMpshippingid($mpShippingId);
                        if ($getImpactPriceArr) {
                            foreach ($getImpactPriceArr as $getImpactPrice) {
                                $objShippingImp = new WkMpSellerShippingImpact((int) $getImpactPrice['id']);
                                $objShippingImp->shipping_delivery_id = (int) $rangeObj->id;
                                $objShippingImp->update();
                            }
                        }
                    }

                    $priceList = [];
                    $priceMethod = Carrier::SHIPPING_METHOD_PRICE;
                    $weightMethod = Carrier::SHIPPING_METHOD_WEIGHT;
                    if (is_array($zoneDetail) && count($zoneDetail)) {
                        foreach ($zoneDetail as $zone) {
                            $zoneId = $zone['id_zone'];
                            $postName = 'zone_' . $zoneId;
                            $isFeeSet = Tools::getValue($postName);
                            if (!empty($isFeeSet)) {
                                if (isset($zoneFees[$zoneId][$key])) {
                                    $price = (float) $zoneFees[$zoneId][$key];
                                } else {
                                    $price = 0;
                                }
                                $priceList[] = [
                                    'id_range_price' => ($rangeType == $priceMethod ? (int) $rangeObj->id : null),
                                    'id_range_weight' => ($rangeType == $weightMethod ? (int) $rangeObj->id : null),
                                    'id_carrier' => (int) $objCarrier->id,
                                    'id_zone' => (int) $zoneId,
                                    'price' => isset($price) ? (float) str_replace(',', '.', $price) : 0,
                                ];
                            }
                        }
                    }
                    if (count($priceList) && !$this->addDeliveryPrice($priceList, true)) {
                        return false;
                    }
                }
            }
        } elseif ($objCarrier->is_free) {
            if (is_array($zoneDetail) && count($zoneDetail)) {
                if (isset($objCarrier->shipping_method) && $rangeType == Carrier::SHIPPING_METHOD_WEIGHT) {
                    $rangeObj = new RangeWeight();
                } else {
                    $rangeObj = new RangePrice();
                }
                $rangeObj->id_carrier = (int) $objCarrier->id;
                $rangeObj->delimiter1 = (float) 0;
                $rangeObj->delimiter2 = (float) 0;
                $rangeObj->save();
                $priceMethod = Carrier::SHIPPING_METHOD_PRICE;
                $weightMethod = Carrier::SHIPPING_METHOD_WEIGHT;
                foreach ($zoneDetail as $zone) {
                    $zoneId = $zone['id_zone'];
                    $price = 0;
                    $priceList[] = [
                        'id_range_price' => ($rangeType == $priceMethod ? (int) $rangeObj->id : null),
                        'id_range_weight' => ($rangeType == $weightMethod ? (int) $rangeObj->id : null),
                        'id_carrier' => (int) $objCarrier->id,
                        'id_zone' => (int) $zoneId,
                        'price' => isset($price) ? (float) str_replace(',', '.', $price) : 0,
                    ];
                }
            }
            if (count($priceList) && !$this->addDeliveryPrice($priceList, true)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Add new delivery prices.
     *
     * @param array $price_list Prices list in multiple arrays (changed to array since 1.5.0)
     * @param bool $delete
     *
     * @return bool
     */
    public function addDeliveryPrice($price_list, $delete = false)
    {
        if (!$price_list) {
            return false;
        }

        $keys = array_keys($price_list[0]);
        if (!in_array('id_shop', $keys)) {
            $keys[] = 'id_shop';
        }
        if (!in_array('id_shop_group', $keys)) {
            $keys[] = 'id_shop_group';
        }

        $sql = 'INSERT INTO `' . _DB_PREFIX_ . 'delivery` (' . implode(', ', $keys) . ') VALUES ';
        foreach ($price_list as $values) {
            $values['id_shop_group'] = $values['id_shop'] = null;
            if ($delete) {
                Db::getInstance()->execute(
                    'DELETE FROM `' . _DB_PREFIX_ . 'delivery`
                    WHERE id_shop IS NOT NULL AND id_shop_group IS NOT NULL
                    AND id_carrier=' . (int) $values['id_carrier'] .
                    ($values['id_range_price'] !== null ? ' AND id_range_price=' . (int) $values['id_range_price'] : ' AND (ISNULL(`id_range_price`) OR `id_range_price` = 0)') .
                    ($values['id_range_weight'] !== null ? ' AND id_range_weight=' . (int) $values['id_range_weight'] : ' AND (ISNULL(`id_range_weight`) OR `id_range_weight` = 0)') . '
                    AND id_zone=' . (int) $values['id_zone']
                );
            }

            $sql .= '(';
            foreach ($values as $v) {
                if (null === $v) {
                    $sql .= 'NULL';
                } elseif (is_int($v) || is_float($v)) {
                    $sql .= $v;
                } else {
                    $sql .= '\'' . Db::getInstance()->escape($v, false, true) . '\'';
                }
                $sql .= ', ';
            }
            $sql = rtrim($sql, ', ') . '), ';
        }
        $sql = rtrim($sql, ', ');

        return Db::getInstance()->execute($sql);
    }

    public function changeZones($objCarrier, $zoneFees)
    {
        $zoneDetail = Zone::getZones(false, true);
        if (is_array($zoneFees)) {
            $updateZones = array_keys($zoneFees);
        } elseif ($objCarrier->is_free && is_array($zoneDetail)) {
            foreach ($zoneDetail as $zone) {
                $updateZones[] = $zone['id_zone'];
            }
        } else {
            $updateZones = [];
        }
        foreach ($zoneDetail as $zone) {
            if (count($objCarrier->getZone($zone['id_zone']))) {
                if (!empty($updateZones) && !in_array($zone['id_zone'], $updateZones)) {
                    $objCarrier->deleteZone($zone['id_zone']);
                }
            } elseif (empty($updateZones) || in_array($zone['id_zone'], $updateZones)) {
                $objCarrier->addZone($zone['id_zone']);
            }
        }

        return true;
    }

    public function updateZoneShop($idCarrier, $idShop = false)
    {
        if (!$idShop) {
            $idShop = (int) Context::getContext()->shop->id;
        }

        if (!$this->carrierAllowedOnShop($idCarrier, $idShop)) {
            return Db::getInstance()->update(
                'carrier_shop',
                [
                    'id_shop' => (int) $idShop,
                ],
                'id_carrier = "' . (int) $idCarrier . '" '
            );
        }
    }

    public function carrierAllowedOnShop($idCarrier, $idShop = false)
    {
        if (!$idShop) {
            $idShop = (int) Context::getContext()->shop->id;
        }
        $shippingInfo = Db::getInstance()->getValue(
            'SELECT `id_shop` FROM `' . _DB_PREFIX_ . 'carrier_shop` WHERE  `id_shop`=' . (int) $idShop . ' AND `id_carrier` = ' . (int) $idCarrier);
        if ($shippingInfo) {
            return $shippingInfo;
        } else {
            return false;
        }
    }

    public function addCarrierTaxRule($idCarrier, $idTaxRuleGroup)
    {
        return Db::getInstance()->insert('carrier_tax_rules_group_shop', [
            'id_carrier' => (int) $idCarrier,
            'id_tax_rules_group' => (int) $idTaxRuleGroup,
            'id_shop' => (int) Context::getContext()->shop->id,
        ]);
    }

    public function changeGroups($idCarrier, $shippingGroup)
    {
        $groups = Db::getInstance()->executeS('SELECT id_group FROM `' . _DB_PREFIX_ . 'group`');
        if (!empty($groups)) {
            foreach ($groups as $group) {
                if (count(self::getCarrierGroup($idCarrier, $group['id_group']))) {
                    continue;
                } elseif (!empty($shippingGroup) && in_array($group['id_group'], $shippingGroup)) {
                    Db::getInstance()->execute('
                            INSERT INTO ' . _DB_PREFIX_ . 'carrier_group (id_group, id_carrier)
                            VALUES(' . (int) $group['id_group'] . ',' . (int) $idCarrier . ')
                        ');
                }
            }
        }
    }

    public static function getCarrierGroup($idCarrier, $idGroup)
    {
        return Db::getInstance()->executeS('
			SELECT *
			FROM `' . _DB_PREFIX_ . 'carrier_group`
			WHERE `id_carrier` = ' . (int) $idCarrier . '
			AND `id_group` = ' . (int) $idGroup);
    }

    public function getMpShippingInfo($mpIdShipping, $idLang)
    {
        $shippingInfo = Db::getInstance()->getRow('SELECT * FROM `' . _DB_PREFIX_ . 'wk_mp_seller_shipping` ms
        INNER JOIN `' . _DB_PREFIX_ . 'carrier` c ON (ms.`id_ps_reference`=c.`id_reference`)
        INNER JOIN `' . _DB_PREFIX_ . 'carrier_lang` cl ON (cl.`id_carrier` = c.`id_carrier` AND cl.`id_lang`=' . $idLang . ')
        INNER JOIN `' . _DB_PREFIX_ . 'carrier_shop` cs ON (cs.`id_carrier`=c.`id_carrier`)
        WHERE ms.`id_wk_mp_shipping`=' . $mpIdShipping . ' AND `deleted` = 0');

        if ($shippingInfo) {
            return $shippingInfo;
        } else {
            return false;
        }
    }

    public function mailToAdminShippingAdded($mpIdSeller, $mpShippingId)
    {
        $objSellerInfo = new WkMpSeller((int) $mpIdSeller);
        $idLang = $objSellerInfo->default_lang; // Seller default lang

        $objSeller = new WkMpSeller($mpIdSeller, $idLang);
        $mpSellerName = $objSeller->seller_firstname . ' ' . $objSeller->seller_lastname;
        $businessEmail = $objSeller->business_email;
        $mpShopName = $objSeller->shop_name;
        $phone = $objSeller->phone;

        if ($businessEmail == '') {
            $idCustomer = $objSeller->seller_customer_id;
            $objCus = new Customer($idCustomer);
            $businessEmail = $objCus->email;
        }

        $shippingInfo = $this->getMpShippingInfo($mpShippingId, $idLang);

        if ($shippingInfo['is_free'] == 0) {
            $freeShipping = 'No';
        } else {
            $freeShipping = 'Yes';
        }

        if ($shippingInfo['shipping_handling'] == 0) {
            $handling = 'No';
        } else {
            $handling = 'Yes';
        }

        if ($shippingInfo['active'] == 0) {
            $status = 'Pending';
        } else {
            $status = 'Approved';
        }

        $templateVars = [
            '{seller_name}' => $mpSellerName,
            '{mp_shop_name}' => $mpShopName,
            '{business_email}' => $businessEmail,
            '{phone}' => $phone,
            '{shipping_name}' => $shippingInfo['name'],
            '{transit_delay}' => $shippingInfo['delay'],
            '{free_shipping}' => $freeShipping,
            '{handling_cost}' => $handling,
            '{status}' => $status,
        ];

        $tempPath = _PS_MODULE_DIR_ . 'marketplace/mails/';

        if (Configuration::get('WK_MP_SUPERADMIN_EMAIL')) {
            $adminEmail = Configuration::get('WK_MP_SUPERADMIN_EMAIL');
        } else {
            $idEmployee = WkMpHelper::getSupperAdmin();
            $employee = new Employee($idEmployee);
            $adminEmail = $employee->email;
        }

        Mail::Send(
            (int) Configuration::get('PS_LANG_DEFAULT'),
            'shipping_added',
            Mail::l('New Shipping method added', (int) Configuration::get('PS_LANG_DEFAULT')),
            $templateVars,
            $adminEmail,
            null,
            null,
            null,
            null,
            null,
            $tempPath,
            false,
            null,
            null
        );
    }

    public function mailToSeller($mpIdSeller, $mpShippingId, $approve)
    {
        $objSellerInfo = new WkMpSeller((int) $mpIdSeller);
        $idLang = $objSellerInfo->default_lang; // Seller default lang

        $objSeller = new WkMpSeller($mpIdSeller, $idLang);
        $mpSellerName = $objSeller->seller_firstname . ' ' . $objSeller->seller_lastname;
        $businessEmail = $objSeller->business_email;
        $mpShopName = $objSeller->shop_name;
        $phone = $objSeller->phone;

        $businessEmail = $objSellerInfo->business_email;
        if ($businessEmail == '') {
            $idCustomer = $objSeller->seller_customer_id;
            $objCus = new Customer($idCustomer);
            $businessEmail = $objCus->email;
        }
        $shippingInfo = $this->getMpShippingInfo($mpShippingId, $idLang);

        if ($shippingInfo['is_free'] == 0) {
            $freeShipping = 'No';
        } else {
            $freeShipping = 'Yes';
        }

        if ($shippingInfo['shipping_handling'] == 0) {
            $handling = 'No';
        } else {
            $handling = 'Yes';
        }

        if ($shippingInfo['active'] == 0) {
            $status = 'Pending';
        } else {
            $status = 'Approved';
        }

        $templateVars = [
            '{seller_name}' => $mpSellerName,
            '{mp_shop_name}' => $mpShopName,
            '{business_email}' => $businessEmail,
            '{phone}' => $phone,
            '{shipping_name}' => $shippingInfo['name'],
            '{transit_delay}' => $shippingInfo['delay'],
            '{free_shipping}' => $freeShipping,
            '{handling_cost}' => $handling,
            '{status}' => $status,
        ];

        $tempPath = _PS_MODULE_DIR_ . 'marketplace/mails/';

        if ($approve == 1) {
            Mail::Send(
                $idLang,
                'shipping_active',
                Mail::l('Shipping method activated', $idLang),
                $templateVars,
                $businessEmail,
                null,
                null,
                null,
                null,
                null,
                $tempPath,
                false,
                null,
                null
            );
        }
        if ($approve == 0) {
            Mail::Send(
                $idLang,
                'shipping_deactive',
                Mail::l('Shipping method deactivated', $idLang),
                $templateVars,
                $businessEmail,
                null,
                null,
                null,
                null,
                null,
                $tempPath,
                false,
                null,
                null
            );
        }
    }

    public function getCarrierRangeValue($carrier)
    {
        $ranges = [];
        $carrierZones = $carrier->getZones();
        $carrierZonesIds = [];
        if (is_array($carrierZones)) {
            foreach ($carrierZones as $carrierZone) {
                $carrierZonesIds[] = $carrierZone['id_zone'];
            }
        }

        $rangeTable = $carrier->getRangeTable();
        $shippingMethod = $carrier->getShippingMethod();

        if ($shippingMethod == Carrier::SHIPPING_METHOD_FREE) {
            $rangeObj = $carrier->getRangeObject($carrier->shipping_method);
            $priceByRange = [];
        } else {
            $rangeObj = $carrier->getRangeObject();
            $priceByRange = Carrier::getDeliveryPriceByRanges($rangeTable, (int) $carrier->id);
        }

        foreach ($priceByRange as $price) {
            $priceByRange[$price['id_' . $rangeTable]][$price['id_zone']] = $price['price'];
        }
        $ranges['price'] = $priceByRange;
        $tmpRange = $rangeObj->getRanges((int) $carrier->id);
        if ($shippingMethod != Carrier::SHIPPING_METHOD_FREE) {
            foreach ($tmpRange as $range) {
                $ranges['range'][$range['id_' . $rangeTable]] = $range;
                $ranges['range'][$range['id_' . $rangeTable]]['id_range'] = $range['id_' . $rangeTable];
            }
        }
        if (!isset($ranges['range'])) {
            $ranges['range'] = [];
        }

        // init blank range
        if (isset($ranges['range']) && !count($ranges['range'])) {
            $ranges['range'][] = ['id_range' => 0, 'delimiter1' => 0, 'delimiter2' => 0];
        }

        return $ranges;
    }

    public static function updateDefaultShipping($mpShippingId, $isDefaultShipping)
    {
        return Db::getInstance()->update('wk_mp_seller_shipping', ['is_default_shipping' => (int) $isDefaultShipping], '`id_wk_mp_shipping` = ' . (int) $mpShippingId);
    }

    public static function getSellerIdByMpShippingId($mpShippingId)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue(
            'SELECT `id_seller` FROM `' . _DB_PREFIX_ . 'wk_mp_seller_shipping` WHERE `id_wk_mp_shipping` = ' . (int) $mpShippingId
        );
    }

    public function getMpShippingForProducts($idPSCarrierRef, $idShop = false)
    {
        if (!$idShop) {
            $idShop = (int) Context::getContext()->shop->id;
        }
        $mpShip = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT * FROM `' . _DB_PREFIX_ . 'product_carrier` WHERE `id_carrier_reference` = ' . (int) $idPSCarrierRef
            . ' AND `id_shop` = ' . (int) $idShop
        );
        if ($mpShip) {
            return $mpShip;
        } else {
            return false;
        }
    }

    public function checkMpProduct($idProduct)
    {
        $mpProduct = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue(
            'SELECT `id_mp_product` FROM `' . _DB_PREFIX_ . 'wk_mp_seller_product`
            WHERE `id_ps_product` = ' . (int) $idProduct
        );
        if ($mpProduct) {
            return $mpProduct;
        }

        return false;
    }

    public function assignAdminDefaultCarriersToSellerProduct($idProduct)
    {
        $adminDefShipping = [];
        if (Configuration::get('MP_SHIPPING_ADMIN_DEFAULT')) {
            $adminDefShipping = json_decode(Configuration::get('MP_SHIPPING_ADMIN_DEFAULT'));
        }
        if ($adminDefShipping) {
            $this->setProductCarrier($idProduct, $adminDefShipping);
        }

        return true;
    }

    public function setProductCarrier($idProduct, $carrierReferenceIds)
    {
        $objProduct = new Product((int) $idProduct);
        if (!$objProduct->is_virtual) { // if not virtual product
            $objProduct->setCarriers($carrierReferenceIds);
        }
    }

    public static function deletePsProductCarrier($psProductId)
    {
        return Db::getInstance()->delete('product_carrier', 'id_product = ' . (int) $psProductId);
    }

    public function getDefaultShippingBySellerId($mpIdSeller)
    {
        return Db::getInstance()->executeS(
            'SELECT `id_wk_mp_shipping`, `id_ps_reference`
            FROM `' . _DB_PREFIX_ . 'wk_mp_seller_shipping`
            WHERE `id_seller`=' . (int) $mpIdSeller .
            ' AND is_default_shipping = 1'
        );
    }

    public static function getSellerAllShippingMethod($mpIdSeller)
    {
        $mpShippingDetail = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT * FROM `' . _DB_PREFIX_ . 'wk_mp_seller_shipping` WHERE `id_seller` = ' . (int) $mpIdSeller
        );

        if (empty($mpShippingDetail)) {
            return false;
        } else {
            return $mpShippingDetail;
        }
    }

    public function assignShippingOnProduct($mpProductId, $updateProduct)
    {
        if ($mpProductId) {
            $objMpSellerProductDetail = new WkMpSellerProduct($mpProductId);
            $mpIdSeller = $objMpSellerProductDetail->id_seller;
            $psProductId = $objMpSellerProductDetail->id_ps_product;

            // check if product choose as virtual product
            $isVirtualProduct = Tools::getValue('mp_is_virtual');
            if (!$isVirtualProduct) {
                if ($updateProduct) {
                    // Delete shipping from seller product in prestashop catalog
                    if ($psProductId) {
                        WkMpSellerShipping::deletePsProductCarrier($psProductId);
                    }
                }

                $carriers = [];
                $mpShippingCarrier = Tools::getValue('carriers');
                if (isset($mpShippingCarrier) && !empty($mpShippingCarrier)) {
                    // if seller select any carrier from list
                    foreach ($mpShippingCarrier as $idPsReference) {
                        $mpShippingId = WkMpSellerShipping::getMpShippingId($idPsReference);
                        if ($mpShippingId) {
                            // if seller shipping
                            $shippingSellerId = WkMpSellerShipping::getSellerIdByMpShippingId($mpShippingId);
                            if ($shippingSellerId == $mpIdSeller) {
                                $carriers[] = (int) $idPsReference;
                            }
                        } else {
                            // if admin carrier
                            $carriers[] = (int) $idPsReference;
                        }
                    }

                    if ($psProductId) {
                        $objProduct = new Product($psProductId);
                        $productStatus = 1; // default active
                        if ($productStatus) {
                            $objProduct->setCarriers($carriers);
                        }
                    }
                } else {
                    if ($psProductId) {
                        $objProduct = new Product($psProductId);
                        $productStatus = 1; // default active
                        if ($productStatus) {
                            // set carrier using carrier reference
                            $objWkMpSellerShipping = new WkMpSellerShipping();
                            $objWkMpSellerShipping->assignAdminDefaultCarriersToSellerProduct($psProductId);
                        }
                    }
                }
            }
        }
    }

    public static function getOldCarrierReferenceStatus($idPsReference)
    {
        return Db::getInstance()->getValue(
            'SELECT `active` FROM `' . _DB_PREFIX_ . 'carrier`
            WHERE `id_reference` = ' . (int) $idPsReference . ' AND deleted = 1 ORDER BY id_carrier DESC'
        );
    }
}
