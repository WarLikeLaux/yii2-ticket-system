<?php

namespace tests\unit\controllers;

use app\controllers\RequestsApiController;
use Yii;
use yii\web\Application;
use yii\web\Response;

class RequestsApiControllerTest extends \Codeception\Test\Unit
{
    private $transaction;

    protected function _before()
    {
        $config = require __DIR__ . '/../../../config/web.php';
        new Application($config);
        $this->transaction = Yii::$app->db->beginTransaction();
    }

    protected function _after()
    {
        $this->transaction->rollback();
    }

    public function testActionIndex()
    {
        $controller = new RequestsApiController('requests', Yii::$app);
        Yii::$app->request->setQueryParams([
            'status' => 'Active',
        ]);
        $response = $controller->runAction('index');
        $this->assertInstanceOf(Response::class, $response);
        $data = $response->data;
        $this->assertIsArray($data, "Response data should be an array.");
        $this->assertArrayHasKey('pagination', $data, "Response data should have a pagination key.");
        $pagination = $data['pagination'];
        $this->assertIsArray($pagination, "Pagination data should be an array.");
        $this->assertArrayHasKey('total_records', $pagination, "Pagination should have total_records key.");
        $this->assertArrayHasKey('current_page', $pagination, "Pagination should have current_page key.");
        $this->assertArrayHasKey('total_pages', $pagination, "Pagination should have total_pages key.");
        $this->assertArrayHasKey('next_page', $pagination, "Pagination should have next_page key.");
        $this->assertArrayHasKey('prev_page', $pagination, "Pagination should have prev_page key.");
        $this->assertIsInt($pagination['total_records'], "Total records should be an integer.");
        $this->assertIsInt($pagination['current_page'], "Current page should be an integer.");
        $this->assertIsInt($pagination['total_pages'], "Total pages should be an integer.");
        $this->assertThat(
            $pagination['next_page'],
            $this->logicalOr($this->isNull(), $this->isType('int')),
            "Next page should be null or an integer."
        );
        $this->assertThat(
            $pagination['prev_page'],
            $this->logicalOr($this->isNull(), $this->isType('int')),
            "Previous page should be null or an integer."
        );
    }

    public function testActionCreateAndUpdate()
    {
        $controller = new RequestsApiController('requests', Yii::$app);
        $jsonInput = json_encode([
            'name' => 'Test Name',
            'email' => 'test@example.com',
            'status' => 'Active',
            'message' => 'Test Message'
        ]);
        Yii::$app->request->setRawBody($jsonInput);
        $createResponse = $controller->runAction('create');
        $this->assertInstanceOf(Response::class, $createResponse);
        $createdData = $createResponse->data->attributes;
        $this->assertIsArray($createdData, "Response data from create should be an array.");
        $this->assertArrayHasKey('id', $createdData, "Created data should have an id key.");
        $createdRequestId = $createdData['id'];
        var_dump($createdRequestId);
        $jsonInput = json_encode(['status' => 'Resolved', 'comment' => 'Test Comment']);
        Yii::$app->request->setRawBody($jsonInput);
        $updateResponse = $controller->runAction('update', ['id' => $createdRequestId]);
        $this->assertInstanceOf(Response::class, $updateResponse);
        $updatedData = $updateResponse->data->attributes;
        $this->assertIsArray($updatedData, "Response data from update should be an array.");
        $this->assertEquals('Resolved', $updatedData['status'], "Status of the updated request should be 'Resolved'.");
    }
}
