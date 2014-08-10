PHP SDK for FairPay
===================

Instalation
-----------

### Avec Composer

Ajoutez cette ligne à votre composer.json
```json
{
    "require": {
        "ferus/fairpay-php-sdk": "dev-master"
    }
}
```

Puis lancez la commande suivante :
```
php composer.phar update
```

### A la main
Copiez le contenue du dossier **src** dans votre projet.


Utilisation
-----------

```php
require_once __DIR__ . '/vendor/autoload.php'; // pour une instalation avec composer
require_once __DIR__ . '/Ferus/FairPayApi/FairPay.php'; // pour une instalation manuelle

use Ferus\FairPayApi\FairPay;

$fairpay = new FairPay();
$fairpay = new FairPay('api_s3cr3t');

$fairpay->setApiKey('api_s3cr3t');
$fairpay->setEndpoint('http://localhost/perso/api');

$fairpay->api('/students');
$fairpay->api('/students/{query}', 'get', array('query' => $query));

// Racourcis
$fairpay->getStudents();
$fairpay->getStudent($query);
$fairpay->searchStudents($query);
$fairpay->getBalance();
$fairpay->cash($client_id, $amount, $cause);
$fairpay->deposit($client_id, $amount);
```

### Gestion des erreurs

```php
use \Ferus\FairPayApi\Exception\CurlExecException;
use \Ferus\FairPayApi\Exception\ApiErrorException;

try{
    $fairpay->cash($client_id, $amount, $cause);
}
catch(CurlExecException $e){
    $e->message; // curl error message
}
catch(ApiErrorException $e){
    $e->message; // api error message
    $e->code; // http status code
    $e->returned_value; // full api responce
}
```