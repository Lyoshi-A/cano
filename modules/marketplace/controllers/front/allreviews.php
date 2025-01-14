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

class MarketplaceAllReviewsModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        $shopLinkRewrite = Tools::getValue('mp_shop_name');
        $mpSeller = WkMpSeller::getSellerByLinkRewrite($shopLinkRewrite, $this->context->language->id);
        if ($mpSeller) {
            $idSeller = $mpSeller['id_seller'];

            $n = Configuration::get('PS_PRODUCTS_PER_PAGE');
            $p = Tools::getValue('p');
            if (!$p) {
                $p = 1; // default page number
            }

            // Review details
            $objReview = new WkMpSellerReview();
            $sellerReview = $objReview->getSellerReviewByIdSeller($idSeller);
            if ($sellerReview) {
                $nbReviews = count($sellerReview);
                $sellerReview = $this->filterReviewsByPage($sellerReview, $p, $n);

                foreach ($sellerReview as &$review) {
                    // get name from prestashop customer table
                    $customer = new Customer($review['id_customer']);
                    $review['customer_name'] = $customer->firstname . ' ' . $customer->lastname;

                    // Get Customer review record - Is helpful or not
                    if ($this->context->customer->id) {
                        $customerReviewDetails = $objReview->isReviewHelpfulForCustomer(
                            $this->context->customer->id,
                            $review['id_review']
                        );
                        if ($customerReviewDetails) {
                            $review['like'] = $customerReviewDetails['like'];
                        }
                    }

                    // Get Total likes(helpful) or dislikes (not helpful) on particular review
                    $reviewDetails = $objReview->getReviewHelpfulSummary($review['id_review']);
                    if ($reviewDetails) {
                        $review['total_likes'] = $reviewDetails['total_likes'];
                        $review['total_dislikes'] = $reviewDetails['total_dislikes'];
                    }
                }

                // Sort review list according to admin configuration (By default it will display sort by recent review)
                if (Configuration::get('WK_MP_REVIEW_DISPLAY_SORT') == '2') { // 2 for most helpful
                    $sellerReview = $objReview->sortingReviewList($sellerReview);
                }

                $this->context->smarty->assign([
                    'reviews_info' => $sellerReview,
                    'shopLinkRewrite' => $shopLinkRewrite,
                    'nbReviews' => $nbReviews,
                    'p' => $p,
                    'n' => $n,
                    'page_count' => (int) ceil($nbReviews / $n),
                    'myAccount' => 'index.php?controller=authentication&back=' . urlencode($this->context->link->getModuleLink('marketplace', 'allreviews', ['mp_shop_name' => $shopLinkRewrite])),
                ]);

                // Assign the seller details view vars
                WkMpSeller::checkSellerAccessPermission($mpSeller['seller_details_access']);

                // Display seller rating summary
                if ($sellerRating = WkMpSellerReview::getSellerAvgRating($idSeller)) {
                    $totalReview = $nbReviews;

                    // Get seller rating full summary
                    $sellerRatingDetail = WkMpSellerReview::getSellerRatingSummary($idSeller, $totalReview);

                    $this->context->smarty->assign(
                        [
                            'sellerRating' => $sellerRating,
                            'sellerRatingDetail' => $sellerRatingDetail,
                            'totalReview' => $totalReview,
                        ]
                    );

                    Media::addJsDef([
                        'sellerRating' => $sellerRating,
                        'rating_start_path' => _MODULE_DIR_ . $this->module->name . '/views/img/',
                        'totalReview' => $totalReview,
                    ]);
                }
            }

            $this->defineJSVars();
            $this->setTemplate('module:marketplace/views/templates/front/seller/allreviews.tpl');
        } else {
            Tools::redirect($this->context->link->getPageLink('pagenotfound'));
        }
    }

    public function defineJSVars()
    {
        $jsVars = [
            'contact_seller_ajax_link' => $this->context->link->getModuleLink('marketplace', 'contactsellerprocess'),
            'logged' => $this->context->customer->isLogged(),
            'some_error' => $this->module->l('Some error occured...', 'allreviews'),
        ];

        Media::addJsDef($jsVars);
    }

    public function filterReviewsByPage($sellerReview, $p, $n)
    {
        $result = [];
        if ($sellerReview) {
            $start = ($p - 1) * $n;
            $end = $start + $n;
            for ($i = $start; $i < $end; ++$i) {
                if (array_key_exists($i, $sellerReview)) {
                    $result[] = $sellerReview[$i];
                }
            }
        }

        return $result;
    }

    public function setMedia()
    {
        parent::setMedia();

        $this->registerStylesheet(
            'mp_store_profile',
            'modules/' . $this->module->name . '/views/css/mp_store_profile.css'
        );

        $this->registerStylesheet(
            'marketplace_account',
            'modules/' . $this->module->name . '/views/css/marketplace_account.css'
        );

        $this->registerStylesheet(
            'mp_seller_rating-css',
            'modules/' . $this->module->name . '/views/css/mp_seller_rating.css'
        );

        $this->registerJavascript(
            'contactseller-js',
            'modules/' . $this->module->name . '/views/js/contactseller.js'
        );

        $this->registerJavascript(
            'mp-jquery-raty-min',
            'modules/' . $this->module->name . '/views/js/libs/jquery.raty.min.js'
        );

        $this->registerJavascript(
            'mp_review_like-js',
            'modules/' . $this->module->name . '/views/js/mp_review_like.js'
        );
    }
}
