<?php
ini_set('display_errors', 1);
error_reporting(-1);

require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\JsonResponse as Response;
use Symfony\Component\HttpFoundation\Request;

$app = new Silex\Application();

$app['debug'] = true;

$app->register(new Yosymfony\Silex\ConfigServiceProvider\ConfigServiceProvider(array(
    __DIR__ . '/../config',
)));

$repository = $app['configuration']->load('foursquare.yml');

$app->register(new TheTwelve\Foursquare\Silex\FoursquareServiceProvider(), array(
    'foursquare.version' => 2,
    'foursquare.endpoint' => 'https://api.foursquare.com',
    'foursquare.clientKey' => 'CurlHttpClient', // some value to force the CurlHttpClient
    'foursquare.pathToCertificate' => '../vendor/haxx-se/curl/cacert.pem',
    'foursquare.clientId' => $repository->get('client_id', 'FOURSQUARE_CLIENT_ID_TBD'),
    'foursquare.clientSecret' => $repository->get('client_secret', 'FOURSQUARE_CLIENT_SECRET_TBD'),
));

$app->get('/venues/search', function (Request $request) use ($app) {

    $gateway = $app['foursquare']->getVenuesGateway();

    $result = $gateway->search($request->query->all());

    return new Response($result, 200, array('Content-Type' => 'application/json'));
});

$app->after(function (Request $request, Response $response) {

});

$app->run();