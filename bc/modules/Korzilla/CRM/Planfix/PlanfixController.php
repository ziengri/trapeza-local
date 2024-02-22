<?php
/**
 * Контроллер работы с Planfix
 * 
 * Ссылка на документацию https://help.planfix.com/ru/%D0%9F%D0%BB%D0%B0%D0%BD%D0%A4%D0%B8%D0%BA%D1%81
 * 
 * @author Олег Хрулёв 
 */
namespace App\modules\Korzilla\CRM\Planfix;

use App\modules\Korzilla\CRM\Planfix\EntityParser\Form as EntityParserForm;
use App\modules\Korzilla\CRM\Planfix\EntityParser\CallbackForm as EntityParserCallbackForm;
use App\modules\Korzilla\CRM\Planfix\EntityParser\Order as EntityParserOrder;
use App\modules\Korzilla\CRM\Planfix\Request\PostConvertor\JsonConvertor;
use App\modules\Korzilla\CRM\Planfix\Request\Request;
use App\modules\Korzilla\CRM\Planfix\Request\Response\Webhook\JsonResponse;
use App\modules\Korzilla\CRM\Planfix\Webhook\WebhookController;
use Exception;

class PlanfixController
{
    /*
        Вебхук для форм:
        Post-запрос в формате json

        Поля: 
        name - Имя (string)
        phone - Телефон (string)
        email - Почта (string)
        description - Комментарий / Описание (text)

        Пример:
        {
            "name": "Иванов Иван Иванович",
            "phone": "89000000000",
            "email": "ivan@nomail.ru",
            "description": "Комментарий: Я хочу преобрести пылесос, но я в них не разбираюься, хотел бы получить консультацию от специалсита.
            Удобное время для звонка: 18:00 - 20:00
            Город: Москва
            Желаемый производитель продукта: Bosh"
        }
    */
    public function sendFormByWebhook($form)
    {
        $formParser = new EntityParserForm($form);
        $request = new Request(new JsonResponse(), new JsonConvertor());
        $request
            ->setUrl((new WebhookController())->getCreateFormLink())
            ->addHeaders('Accept: application/json')
            ->addPost('name', $formParser->getClientName())
            ->addPost('phone', $formParser->getClientPhone())
            ->addPost('email', $formParser->getClientEmail())
            ->addPost('description', $formParser->getDescription())
        ;
        
        $response = $request->handle();

        if ($response->isError()) {
            $message = 'Ошибка отправки формы в планфикс.';
            $message .= PHP_EOL.'Id формы: '.$form['Message_ID'];
            $message .= PHP_EOL.'Ответ от сервиса: '.$response->getErrorMessage();
            throw new Exception($message);
        }

        return $response->normalize();
    }

    public function sendCallbackFormByWebhook($form)
    {
        $formParser = new EntityParserCallbackForm($form);
        $request = new Request(new JsonResponse(), new JsonConvertor());
        $request
            ->setUrl((new WebhookController())->getCreateFormLink())
            ->addHeaders('Accept: application/json')
            ->addPost('name', $formParser->getClientName())
            ->addPost('phone', $formParser->getClientPhone())
            ->addPost('email', $formParser->getClientEmail())
            ->addPost('description', $formParser->getDescription())
        ;
        
        $response = $request->handle();

        if ($response->isError()) {
            $message = 'Ошибка отправки формы в планфикс.';
            $message .= PHP_EOL.'Id формы: '.$form['Message_ID'];
            $message .= PHP_EOL.'Ответ от сервиса: '.$response->getErrorMessage();
            throw new Exception($message);
        }

        return $response->normalize();
    }

    /*
        Вебхук для заказов:
        Post-запрос в формате json

        Поля: 
        name - Имя покупателя (string)
        phone - Телефон покупателя (string)
        email - Почта покупателя (string)
        comment - Комментарий к заказу от покупятеля (text)
        order_id - Номер заказа на сайте (integer)
        order_sum - Сумма заказа (float)
        order_list - Спсиок товаров (text)
        delivery_address - Адрес доставки (string)

        Пример:
        {
            "name": "Иванов Иван Иванович",
            "phone": "89000000000",
            "email": "ivan@nomail.ru",
            "comment": "Добавьте дополнительный комплект столовых приборов",
            "order_id": "123",
            "order_sum": "578.90",
            "order_list": "<a href=\"site.ru/tovar1.html\" target=\"_blank\">Товар 1</a> - 5шт
            <a href=\"site.ru/tovar2.html\" target=\"_blank\">Товар 2</a> - 3шт
            <a href=\"site.ru/tovar3.html\" target=\"_blank\">Товар 3</a> - 1шт",
            "delivery_address": "Улица пушкина, дом 1, кв 16"
        }
    */
    public function sendOrderByWebhook($order)
    {
        $orderParser = new EntityParserOrder($order);
        $request = new Request(new JsonResponse(), new JsonConvertor());
        $request
            ->setUrl((new WebhookController())->getCreateOrderLink())
            ->addHeaders('Accept: application/json')
            ->addPost('name', $orderParser->getClientName())
            ->addPost('phone', $orderParser->getClientPhone())
            ->addPost('email', $orderParser->getClientEmail())
            ->addPost('comment', $orderParser->getClientComment())
            ->addPost('order_id', $orderParser->getOrderId())
            ->addPost('order_sum', $orderParser->getOrderSum())
            ->addPost('order_list', $orderParser->getOrderList())
            ->addPost('delivery_address', $orderParser->getDeliveryAddress())
        ;

        $response = $request->handle();
        
        if ($response->isError()) {
            $message = 'Ошибка отправки заказа в планфикс.';
            $message .= PHP_EOL.'Id заказа: '.$order['Message_ID'];
            $message .= PHP_EOL.'Ответ от сервиса: '.$response->getErrorMessage();
            throw new Exception($message);
        }
        
        return $response->normalize();
    }
}