<?php declare(strict_types=1);
/**
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the MultiSafepay plugin
 * to newer versions in the future. If you wish to customize the plugin for your
 * needs please document your changes and make backups before you update.
 *
 * @category    MultiSafepay
 * @package     Connect
 * @author      TechSupport <integration@multisafepay.com>
 * @copyright   Copyright (c) MultiSafepay, Inc. (https://www.multisafepay.com)
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR
 * PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN
 * ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 */

use MultiSafepay\PrestaShop\Services\SdkService;
use MultiSafepay\PrestaShop\Services\OrderService;
use MultiSafepay\Api\Transactions\OrderRequest;
use MultiSafepay\Api\Transactions\TransactionResponse;

class MultisafepayPaymentModuleFrontController extends ModuleFrontController
{

    /**
     * Process checkout form and register the order.
     *
     * @todo Get the initializes order status. Now is hardcoded.
     * @todo Log steps
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function postProcess()
    {
        if ($this->module->active == false) {
            die;
        }

        $cart_id      = $this->context->cart->id;
        $customer_id  = $this->context->cart->id_customer;
        $currency_id  = $this->context->cart->id_currency;
        $amount       = $this->context->cart->getOrderTotal();
        $amount       = 10;
        $secure_key   = $this->context->customer->secure_key;

        $this->context->cart     = new Cart((int) $cart_id);
        $this->context->customer = new Customer((int) $customer_id);
        $this->context->currency = new Currency((int) Context::getContext()->cart->id_currency);
        $this->context->language = new Language((int) Context::getContext()->customer->id_lang);

        if ($this->isValidOrder() === true) {
            // Get Initialized order status ID
            $payment_status = 43;
            $message = null;
        } else {
            $payment_status = Configuration::get('PS_OS_ERROR');
            $message = $this->module->l('An error occurred while processing payment');
        }

        $module_name = $this->module->displayName;

        try {
            $validate = $this->module->validateOrder($cart_id, $payment_status, $amount, $module_name, $message, array(), $currency_id, false, $secure_key);
        } catch (PrestaShopException $prestaShopException) {

        }

        $order = Order::getByCartId($cart_id);
        $order_service = new OrderService($order, $this->module->id, $secure_key);
        $order_request = $order_service->createOrderRequest();
        $transaction = $this->createMultiSafepayTransaction($order_request);
        Tools::redirectLink($transaction->getPaymentUrl());

    }

    /**
     * Create a MultiSafepay Transaction
     *
     * @todo Log errors
     *
     * @param OrderRequest $order_request
     * @return TransactionResponse
     */
    private function createMultiSafepayTransaction( OrderRequest $order_request ): TransactionResponse
    {
        $multisafepay_sdk    = (new SdkService())->getSdk();
        $transaction_manager = $multisafepay_sdk->getTransactionManager();

        try {
            $transaction = $transaction_manager->create( $order_request );
        } catch ( ApiException $api_exception ) {
            // Log error
        }
        return $transaction;
    }

    /**
     * @todo Check if there is something to check internally to validate the order
     *
     * @return bool
     */
    protected function isValidOrder()
    {
        return true;
    }
}
