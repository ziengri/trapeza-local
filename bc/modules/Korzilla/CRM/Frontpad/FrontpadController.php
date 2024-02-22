<?php
/**
 * Контроллер работы с Frontpad
 * 
 * Реализован паттерн Facade https://refactoring.guru/design-patterns/facade
 * 
 * Ссылка на документацию API https://docs.google.com/document/d/1gs81CYvJ6FD9KOseL3GOcrcR2YnEvjQqJn9mJRRc5Yk/edit
 * 
 * @author Олег Хрулёв 
 */
namespace App\modules\Korzilla\CRM\Frontpad;

use App\modules\Korzilla\CRM\Frontpad\Export\ExportUsynchronicProducts;
use App\modules\Korzilla\CRM\Frontpad\Export\UpdateSyncedProducts;
use App\modules\Korzilla\CRM\Frontpad\Request\PostConvertor;
use App\modules\Korzilla\CRM\Frontpad\Request\Request;
use App\modules\Korzilla\CRM\Frontpad\Request\Response;

use Exception;

class FrontpadController
{
    /**
     * Frontpad подключен
     */
    public function isOn(): bool
    {
        try {
            if ($this->getSecret()) {
                return true;
            }
        } catch (Exception $e) {}

        return false;
    }

    /**
     * Получить список товаров из Frontpad
     */
    public function getNumenclature(): array
    {
        $request = $this->createRequest();

        $request
            ->setUrl($request::API_URL)
            ->addGet('get_products', '')
            ->addPost('secret', $this->getSecret())
        ;

        $response = $request->handle();

        $this->checkResponse($response);

        return $response->normalize();
    }

    /**
     * Отправить заказ во Frontpad
     */
    public function sendOrder($order)
    {
        $orderParser = $this->getOrderParser($order);

        $request = $this->createRequest();

        $request
            ->setUrl($request::API_URL)
            ->addGet('new_order', '')
            ->addPost('secret', $this->getSecret())
            ->addPost('product', $orderParser->getProduct())
            ->addPost('product_kol', $orderParser->getProductCount())
            ->addPost('product_mod', $orderParser->getProductMod())
            ->addPost('product_price', $orderParser->getProductPrice())
            ->addPost('street', $orderParser->getAddressStreet())
            ->addPost('home', $orderParser->getAddressHome())
            ->addPost('pod', $orderParser->getAddressPorch())
            ->addPost('et', $orderParser->getAddressFloor())
            ->addPost('apart', $orderParser->getAddressApartment())
            ->addPost('name', $orderParser->getClientName())
            ->addPost('phone', $orderParser->getClientPhone())
            ->addPost('mail', $orderParser->getClientEmail())
            ->addPost('descr', $orderParser->getComment())

            # не настроенные            
            ->addPost('score', $orderParser->getScore())
            ->addPost('sale', $orderParser->getSale())
            ->addPost('sale_amount', $orderParser->getSaleAmount())
            ->addPost('card', $orderParser->getClientCard())
            ->addPost('pay', $orderParser->getPay())
            ->addPost('certificate', $orderParser->getCerificate())
            ->addPost('person', $orderParser->getPersonCount())
            ->addPost('datetime', $orderParser->getDateTime())
        ;

        $response = $request->handle();
        $this->checkResponse($response);

        return $response->normalize();
    }

    /**
     * @param int $catalogueId id сайта
     * @param int $subId id раздела в который выгружать
     * 
     * @return bool
     * 
     * @throws \Exeption
     */
    public function ExportUsynchronicProducts(int $catalogueId, int $subId): bool
    {
        $exporter = new ExportUsynchronicProducts($this->getNumenclature(), $catalogueId, $subId);
        $exporter->handle();
        return true;
    }

    /**
     * @param int $catalogueId id сайта
     * 
     * @return bool
     * 
     * @throws \Exeption
     */
    public function updateSyncedProducts(int $catalogueId): bool
    {
        $exporter = new UpdateSyncedProducts($this->getNumenclature(), $catalogueId);
        $exporter->handle();
        return true;
    }

    /**
     * @param array $order
     * 
     * @return OrderParser
     */
    public function getOrderParser($order): OrderParser
    {
        global $current_catalogue;

        if ($current_catalogue['customCode'] && class_exists('\Custom\Modules\Korzilla\CRM\Frontpad\OrderParser')) {
            return new \Custom\Modules\Korzilla\CRM\Frontpad\OrderParser($order);
        }

        return new OrderParser($order);
    }

    private function createRequest(): Request
    {
        return new Request(new Response(), new PostConvertor());
    }

    private function getSecret(): string
    {
        global $setting;

        if (empty($setting['frontpadSecret'])) {
            throw new Exception('Не установлен ключ Frontpad');
        }

        return $setting['frontpadSecret'];
    }

    private function checkResponse(Response $response)
    {
        if ($response->isError()) {
            $message  = "Ошибка запроса.";
            $message .= " Код ошибки: ".$response->getErrorCode().".";
            $message .= " Текст ошибки: ".htmlspecialchars($response->getErrorMessage()).".";

            throw new Exception($message, $response->getErrorCode());
        }
    }
}
