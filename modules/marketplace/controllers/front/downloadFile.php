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

class MarketplaceDownloadFileModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        if ($this->context->customer->id && !Tools::getValue('admin')) {
            $idCustomer = $this->context->customer->id;
            $mpSellerDetail = WkMpSeller::getSellerDetailByCustomerId($idCustomer);
            if ($mpSellerDetail && $mpSellerDetail['active']) {
                $mpSellerDetail = $mpSellerDetail['id_seller'];
                if (Tools::getValue('id_value')) {
                    $idDownload = Tools::getValue('id_value');
                    $mpProductDetail = WkMpSellerProduct::getSellerProductByIdProduct($idDownload);
                    if ($mpProductDetail['id_seller'] == $mpSellerDetail) {
                        $this->downloadVirtualContent($mpProductDetail);
                    }
                }
            }
        } elseif (Tools::getValue('admin')) {
            $idDownload = Tools::getValue('id_value');
            $mpProductDetail = WkMpSellerProduct::getSellerProductByIdProduct($idDownload);
            $this->downloadVirtualContent($mpProductDetail);
        }

        parent::initContent();
    }

    public function downloadVirtualContent($mpProductDetail)
    {
        $mpProductId = $mpProductDetail['id_mp_product'];
        $psProductId = $mpProductDetail['id_ps_product'];
        $objMpVirtualProduct = new WkMpVirtualProduct();
        if ($psProductId && $objMpVirtualProduct->isMpProductIsVirtualProduct($mpProductId)) {
            $objProductDownload = new ProductDownload();
            $fileKey = $objProductDownload->getFilenameFromIdProduct($psProductId);
            if (!Validate::isSha1($fileKey)) {
                $this->errors[] = $this->module->l('Invalid Key', 'downloadFile');
            }
            $file = _PS_DOWNLOAD_DIR_ . preg_replace('/\.{2,}/', '.', $fileKey);
            $fileName = ProductDownload::getFilenameFromFilename($fileKey);
            if (!file_exists($file)) {
                $this->errors[] = $this->module->l('Image Not exist', 'downloadFile');
            } else {
                set_time_limit(0);
                $this->downloadVirtualFile($file, '' . $fileName . '', '');
            }
        }
    }

    /**
     * [downloadFile -> php download code].
     *
     * @param [type] $file      [file]
     * @param [type] $name      [name of file]
     * @param string $mimeType [file type (format)]
     *
     * @return void
     */
    public function downloadVirtualFile($file, $name, $mimeType = '')
    {
        if (!is_readable($file)) {
            exit('File not found or inaccessible!');
        }
        $mimeType = false;
        if (function_exists('mime_content_type')) {
            $mimeType = @mime_content_type($file);
        }
        $size = filesize($file);
        $name = rawurldecode($name);
        $knownMimeTypes = WkMpHelper::getAllMimeType();

        if (($mimeType == '') || !$mimeType) {
            $fileExtension = Tools::strtolower(Tools::substr(strrchr($file, '.'), 1));
            if (array_key_exists($fileExtension, $knownMimeTypes)) {
                $mimeType = $knownMimeTypes[$fileExtension];
            } else {
                $mimeType = 'application/force-download';
            }
        }
        foreach ($knownMimeTypes as $ext => $meme) {
            if ($mimeType == $meme) {
                $bName = explode('.', $name);
                $bName = $bName[0];
                $name = $bName . '.' . $ext;
                break;
            }
        }
        @ob_end_clean();
        if (ini_get('zlib.output_compression')) {
            ini_set('zlib.output_compression', 'Off');
        }

        header('Content-Type: ' . $mimeType);
        header('Content-Disposition: attachment; filename="' . $name . '"');
        header('Content-Transfer-Encoding: binary');
        header('Accept-Ranges: bytes');
        header('Cache-control: private');
        header('Pragma: private');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

        if (isset($_SERVER['HTTP_RANGE'])) {
            list($a, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
            list($range) = explode(',', $range, 2);
            list($range, $rangeEnd) = explode('-', $range);
            $range = (int) $range;

            if (!$rangeEnd) {
                $rangeEnd = $size - 1;
            } else {
                $rangeEnd = (int) $rangeEnd;
            }

            $newLength = $rangeEnd - $range + 1;
            header('HTTP/1.1 206 Partial Content');
            header("Content-Length: $newLength");
            header("Content-Range: bytes $range-$rangeEnd/$size");
        } else {
            $newLength = $size;
            header('Content-Length: ' . $size);
        }

        $chunkSize = 1 * (1024 * 1024);
        $bytesSend = 0;
        if ($file = fopen($file, 'r')) {
            if (isset($_SERVER['HTTP_RANGE'])) {
                fseek($file, $range);
            }
            while (!feof($file) && (!connection_aborted()) && ($bytesSend < $newLength)) {
                $buffer = fread($file, $chunkSize);
            }
            echo $buffer;
            flush();
            $bytesSend += Tools::strlen($buffer);
        }

        fclose($file);
    }
}
