# api-client-tradesoft
API client tradesoft.ru

# Install
``` sh
composer require r0dgerv/api-client-tradesoft=dev-master
```

Ниже идут примеры применения, полную документацию документацию по [API] можно найти на официальном сайте https://www.tradesoft.ru/

### Список доступных поставщиков
```php
use R0dgerV\ApiClientTradesoft\ApiClient;

$client = new ApiClient('YouLogin', 'YouPassword');
$result = $client->getProviderList();
echo json_encode($result, true)
```

### Поиск производителей по коду getProducerList
```php
use R0dgerV\ApiClientTradesoft\ApiClient;

$client = new ApiClient('YouLogin', 'YouPassword');
$result = $client->generateProviderContentForProducerList(
                'portal_absauto',
                'YouLoginProducer',
                'YouPasswordProducer',
                'kl9'
            )->generateProviderContentForProducerList(
                'adeo',
                'YouLoginProducer',
                'YouPasswordProducer',
                'kl9'
            )
            ->getProducerList();
echo json_encode($result, true)
```


### Запрос списока доступных опций поставщика
```php
use R0dgerV\ApiClientTradesoft\ApiClient;

$client = new ApiClient('YouLogin', 'YouPassword');
$result = $client->generateProviderContentForOptionsList(
                'portal_absauto',
                'YouLoginProducer',
                'YouPasswordProducer',
            )->generateProviderContentForOptionsList(
                'adeo',
                'YouLoginProducer',
                'YouPasswordProducer',
            )
            ->getOptionsList();
echo json_encode($result, true)
```

### Поиск цен и наличия по коду производителю getPriceList
```php
use R0dgerV\ApiClientTradesoft\ApiClient;

$client = new ApiClient('YouLogin', 'YouPassword');
$result = $client->generateProviderContentForPriceList(
                'portal_absauto',
                'YouLoginProducer',
                'YouPasswordProducer',
                'kl9',
                'MAHLE', ['analogs' => 'N']
            )->generateProviderContentForPriceList(
                'adeo',
                'YouLoginProducer',
                'YouPasswordProducer',
                'kl9',
                'MAHLE', ['analogs' => 'N']
            )
            ->getPriceList();
echo json_encode($result, true)
```


[API]: <https://www.tradesoft.ru/help/service/about.php>
