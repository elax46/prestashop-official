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

namespace MultiSafepay\PrestaShop\Services;

use MultiSafepay\Api\Transactions\OrderRequest\Arguments\GatewayInfo\Ideal;
use MultiSafepay\Api\Transactions\OrderRequest\Arguments\GatewayInfoInterface;
use MultiSafepay\PrestaShop\PaymentOptions\Base\BaseGatewayInfo;

/**
 * This class returns the SDK object.
 *
 */
class GatewayInfoService
{

    /**
     * @param string $gateway_code
     * @param array $data
     * @return GatewayInfoInterface
     */
    public function getGatewayInfo(string $gateway_code, array $data): GatewayInfoInterface
    {
        if ('IDEAL' === $gateway_code) {
            return $this->getIdealGatewayInfo($data['issuer_id']);
        }
        return new BaseGatewayInfo();
    }

    /**
     * @param string $issuer_id
     * @return GatewayInfoInterface
     */
    public function getIdealGatewayInfo(string $issuer_id): GatewayInfoInterface
    {
        $gateway_info = new Ideal();
        $gateway_info->addIssuerId($issuer_id);
        return $gateway_info;
    }
}
