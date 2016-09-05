<?php

use TSH\Local\PaymentsController;
use TSH\Local\PaymentsModel;
use TSH\Local\PaymentsRequest;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app = new \Silex\Application();

$app['config'] = require __DIR__ . '/config/config.php';

$app->get('/', function(Request $request) use ($app) {

    $model = new PaymentsModel();
    $controller = new PaymentsController($app['config'], $model);

    $data = $controller->respondTo(new PaymentsRequest(
        $request->query->getInt('page', 1),
        $request->query->get('supplier', ''),
        $request->query->getInt('cost_rating', 0)
    ));

    $content = require __DIR__ . '/templates/index.phtml';

    return new Response($content, 200, [
        'Content-Type' => 'text/html;charset=UTF-8'
    ]);
});

$app->get('/json', function(Request $request) use ($app) {

    $model = new PaymentsModel();
    $controller = new PaymentsController($app['config'], $model);

    $data = $controller->respondTo(new PaymentsRequest(
        $request->query->getInt('page', 1),
        $request->query->get('supplier', ''),
        $request->query->getInt('cost_rating', 0)
    ));

    $data->payments = array_map(function(PaymentsModel $payment) {
        return [
            'supplier' => $payment->supplier,
            'ref' => $payment->ref,
            'cost_rating' => $payment->cost_rating,
            'amount' => $payment->amount,
        ];
    }, $data->payments);

    return $app->json($data, 200);
});


return $app;