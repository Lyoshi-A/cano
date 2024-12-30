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

class AdminSellerReviewsController extends ModuleAdminController
{
    public function __construct()
    {
        $this->context = Context::getContext();
        $this->bootstrap = true;
        $this->table = 'wk_mp_seller_review';
        $this->className = 'WkMpSellerReview';
        $this->list_id = 'id_review';
        $this->identifier = 'id_review';

        parent::__construct();
        $this->toolbar_title = $this->l('Seller Reviews');

        if (!Tools::getValue('id_review')) {
            $this->_defaultOrderBy = 'id_review';
            $this->_select = 'msi.`business_email` AS seller_email,
			msi.`shop_name_unique`,
			COUNT(a.`id_seller`) AS count_seller_reviews,
			COUNT(CASE WHEN a.`active` = 0 THEN 1 ELSE NULL END) AS inactive_review,
			AVG(a.`rating`) AS avg_seller_rating';

            $this->_join = 'LEFT JOIN `' . _DB_PREFIX_ . 'wk_mp_seller` msi ON (a.`id_seller` = msi.`id_seller`)';
            $this->_join .= 'LEFT JOIN `' . _DB_PREFIX_ . 'customer` c ON (c.`id_customer` = msi.`seller_customer_id`)';
            $this->_where = WkMpSeller::addSqlRestriction('msi');
            $this->_group = 'GROUP BY a.`id_seller`';
        }

        $this->fields_list = [
            'id_review' => [
                'title' => $this->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ],
            'shop_name_unique' => [
                'title' => $this->l('Shop name'),
                'align' => 'center',
                'havingFilter' => true,
            ],
            'seller_email' => [
                'title' => $this->l('Seller email'),
                'align' => 'center',
                'havingFilter' => true,
            ],
            'avg_seller_rating' => [
                'title' => $this->l('Avg. rating'),
                'align' => 'center',
                'search' => false,
                'havingFilter' => true,
                'callback' => 'showRatingStar',
            ],
            'count_seller_reviews' => [
                'title' => $this->l('Total review'),
                'align' => 'center',
                'havingFilter' => true,
            ],
            'inactive_review' => [
                'title' => $this->l('Pending reviews'),
                'align' => 'center',
                'havingFilter' => true,
                'badge_warning' => true,
            ],
        ];

        if (!Tools::getValue('id_review')) {
            $this->addRowAction('view');
        }
        $this->addRowAction('delete');
    }

    public function showRatingStar($val, $arr)
    {
        if (isset($this->display)) {
            $this->context->smarty->assign([
                'seller_wise_rating' => 1,
            ]);
        }
        $this->context->smarty->assign([
            'sellerRating' => $val,
            'list' => $arr,
            'rating_start_path' => _MODULE_DIR_ . $this->module->name . '/views/img/',
        ]);

        return $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'marketplace/views/templates/admin/seller_rating.tpl');
    }

    public function initToolbar()
    {
        parent::initToolbar();
        unset($this->toolbar_btn['new']);
    }

    public function getList($id_lang, $orderBy = null, $orderWay = null, $start = 0, $limit = null, $id_lang_shop = null)
    {
        parent::getList($id_lang, $orderBy, $orderWay, $start, $limit, $id_lang_shop);

        if ($this->_list) {
            foreach ($this->_list as &$row) {
                $row['badge_warning'] = $row['inactive_review'] >= 1;
            }
        }
    }

    public function renderView()
    {
        if (($id_review = Tools::getValue('id_review')) && Tools::getIsset('viewwk_mp_seller_review') && (!Tools::getValue('view_review') || Tools::getValue('submitFiltertemp_id_review'))) {
            $obj_review = new WkMpSellerReview($id_review);
            $this->list_no_link = true;

            $this->_select .= 'msi.`business_email` AS seller_email, msi.`shop_name_unique`,
            a.`id_seller` AS count_seller_reviews,
            a.`active` AS inactive_review,
            a.`rating` AS avg_seller_rating';
            $this->_join .= 'JOIN `' . _DB_PREFIX_ . 'wk_mp_seller` msi ON (a.`id_seller` = msi.`id_seller`)';
            $this->_join .= 'LEFT JOIN `' . _DB_PREFIX_ . 'customer` c ON (c.`id_customer` = msi.`seller_customer_id`)';
            $this->_where = WkMpSeller::addSqlRestriction('msi');
            $this->_where .= ' AND a.`id_seller` = ' . (int) $obj_review->id_seller;

            $this->table = 'wk_mp_seller_review';
            $this->className = 'WkMpSellerReview';
            $this->identifier = 'id_review';
            $this->list_id = 'temp_id_review';

            if (!Validate::isLoadedObject(new WkMpSellerReview((int) $id_review))) {
                $this->errors[] = $this->l('An error occurred while updating the status for an object.') . ' ' . $this->table . ' ' . $this->l('(cannot load object)');

                return;
            }

            $this->fields_list = [
                'id_review' => [
                    'title' => $this->l('ID'),
                    'align' => 'center',
                    'class' => 'fixed-width-xs',
                    'orderby' => false,
                    'havingFilter' => true,
                ],
                'customer_email' => [
                    'title' => $this->l('Customer email'),
                    'align' => 'center',
                    'orderby' => false,
                ],
                'rating' => [
                    'title' => $this->l('Rating'),
                    'align' => 'center',
                    'havingFilter' => true,
                    'search' => false,
                    'orderby' => false,
                    'callback' => 'showRatingStar',
                ],
                'review' => [
                    'title' => $this->l('Comment'),
                    'align' => 'text-left',
                    'maxlength' => 50,
                    'orderby' => false,
                    'havingFilter' => true,
                ],
                'date_add' => [
                    'title' => $this->l('Date'),
                    'type' => 'datetime',
                    'orderby' => false,
                    'havingFilter' => true,
                ],
                'active' => [
                    'title' => $this->l('Status'),
                    'active' => 'status',
                    'type' => 'bool',
                    'orderby' => false,
                    'havingFilter' => true,
                ],
            ];

            $this->bulk_actions = [
                'delete' => [
                    'text' => $this->l('Delete selected'),
                    'icon' => 'icon-trash',
                    'confirm' => $this->l('Delete selected items?'),
                ],
            ];

            self::$currentIndex = self::$currentIndex . '&id_review=' . $id_review . '&viewwk_mp_seller_review';
            $this->context->smarty->assign([
                'current' => self::$currentIndex,
            ]);

            if (Tools::isSubmit('submitFilter')) {
                $this->processFilter();
            }

            if (Tools::isSubmit('submitResettemp_id_review')) {
                $this->processResetFilters();
            }

            return parent::renderList();
        } else {
            return parent::renderView();
        }
    }

    // Enable and disable the review by bulkaction tab
    public function reviewsStatusSet($status)
    {
        $result = true;
        if ($id_reviews_list = Tools::getValue('temp_id_reviewBox')) {
            foreach ($id_reviews_list as $reviews_list) {
                $object_review = new WkMpSellerReview($reviews_list);
                $object_review->active = $status;
                $result &= $object_review->update();
            }
        }

        if (is_array($id_reviews_list) && !empty($id_reviews_list)) {
            Tools::redirectAdmin(self::$currentIndex . '&conf=5&token=' . $this->token . '&viewwk_mp_seller_review&id_review=' . $reviews_list);
        } else {
            $this->errors[] = $this->l('You must select at least one item to perform a bulk action.');
        }
    }

    public function postProcess()
    {
        if (Tools::getIsset('deletewk_mp_seller_review') && $idReview = Tools::getValue('id_review')) {
            $deleteReviews = new WkMpSellerReview($idReview);
            $deleteReviews->delete();
            $idNextReview = '';
            if ($sellerReviews = WkMpSellerReview::getSellerReviewByIdSeller($deleteReviews->id_seller)) {
                $idNextReview = '&viewwk_mp_seller_review&id_review=' . $sellerReviews[0]['id_review'];
            } elseif ($sellerReviews = WkMpSellerReview::getSellerReviewByIdSeller($deleteReviews->id_seller, false)) {
                $idNextReview = '&viewwk_mp_seller_review&id_review=' . $sellerReviews[0]['id_review'];
            }
            Tools::redirectAdmin(self::$currentIndex . $idNextReview . '&token=' . $this->token . '&conf=1');
        }
        if (Tools::isSubmit('status' . $this->table)) {
            $idReview = Tools::getValue('id_review');
            $objectReview = new WkMpSellerReview($idReview);
            $objectReview->active = !$objectReview->active;
            if ($objectReview->update()) {
                Tools::redirectAdmin(
                    $this->context->link->getAdminLink('AdminSellerReviews') .
                    '&viewwk_mp_seller_review&conf=5&id_review=' . $idReview
                );
            }
        }

        if (Tools::getValue('view_review') == 1 && !Tools::getValue('submitFiltertemp_id_review')) {
            $id_review = Tools::getValue('id_review');
            $obj_review = new WkMpSellerReview($id_review);
            // get seller information
            $obj_mp_seller = new WkMpSeller($obj_review->id_seller);

            // get customer information
            if ($obj_review->id_customer) {
                // if not a guest
                $obj_customer = new Customer($obj_review->id_customer);
                $customer_name = $obj_customer->firstname . ' ' . $obj_customer->lastname;
                $this->context->smarty->assign('customer_name', $customer_name);
            }

            $this->context->smarty->assign(
                [
                    'review_detail' => $obj_review,
                    'obj_mp_seller' => $obj_mp_seller,
                    'module_dir' => _MODULE_DIR_,
                    'id_review' => $id_review,
                ]
            );
        }

        // Bulk action operations perform
        if (Tools::isSubmit('submitBulkdeletewk_mp_seller_review')) {
            if ($id_reviews_list = Tools::getValue('temp_id_reviewBox')) {
                foreach ($id_reviews_list as $reviews_list) {
                    $delete_reviews_rec = new WkMpSellerReview($reviews_list);
                    $delete_reviews_rec->delete();
                }
                Tools::redirectAdmin(self::$currentIndex . '&token=' . $this->token . '&conf=1');
            }
        } elseif (Tools::isSubmit('submitBulkenableSelectionwk_mp_seller_review')) {
            $this->reviewsStatusSet(1);
        } elseif (Tools::isSubmit('submitBulkdisableSelectionwk_mp_seller_review')) {
            $this->reviewsStatusSet(0);
        }

        if (Tools::isSubmit('temp_id_reviewOrderway')) {
            $this->processFilter();
        }
        parent::postProcess();
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addJS(_MODULE_DIR_ . $this->module->name . '/views/js/libs/jquery.raty.min.js');
    }
}
