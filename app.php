<?php

use TSH\Local\MySqlPaymentsModel;
use TSH\Local\PaymentEntity;
use TSH\Local\PaymentsController;
use TSH\Local\PaymentsRequest;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app = new \Silex\Application();

$app['payment_model'] = $app->factory(function () use ($app) {
    return new MySqlPaymentsModel();
});

$app['controller'] = $app->factory(function () use ($app) {
    return new PaymentsController($app['config'], $app['payment_model']);
});

$app->get('/', function(Request $request) use ($app) {
    $controller = $app['controller'];

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
    $controller = $app['controller'];

    $data = $controller->respondTo(new PaymentsRequest(
        $request->query->getInt('page', 1),
        $request->query->get('supplier', ''),
        $request->query->getInt('cost_rating', 0)
    ));

    $data->payments = array_map(function(PaymentEntity $payment) {
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