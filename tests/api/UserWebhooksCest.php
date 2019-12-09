<?php

namespace Gewaer\Tests\api;

use ApiTester;
use Phalcon\Security\Random;

class UserWebhooksCest extends BakaRestTest
{
    protected $model = 'user-webhooks';

    /**
     * Create
     *
     * @param ApiTester $I
     * @return void
     */
    public function create(ApiTester $I) : void
    {
        $userData = $I->apiLogin();
        $random = new Random();
        $webhookName = 'test_' . $random->base58();

        $I->haveHttpHeader('Authorization', $userData->token);
        $I->sendPost('/v1/' . $this->model, [
            'webhooks_id' => 1,
            'url' => $webhookName,
            'method' => 'POST',
            'format' => 'JSON',
        ]);

        $I->seeResponseIsSuccessful();
        $response = $I->grabResponse();
        $data = json_decode($response, true);

        $I->assertTrue($data['url'] == $webhookName);
    }

    /**
     * update
     *
     * @param ApiTester $I
     * @return void
     */
    public function update(ApiTester $I) : void
    {
        $userData = $I->apiLogin();
        $random = new Random();
        $webhookName = 'test_' . $random->base58();

        $I->haveHttpHeader('Authorization', $userData->token);
        $I->sendGet('/v1/' . $this->model);

        $I->seeResponseIsSuccessful();
        $response = $I->grabResponse();
        $data = json_decode($response, true);

        $I->sendPUT('/v1/' . $this->model . '/' . $data[count($data) - 1]['id'], [
            'url' => $webhookName
        ]);

        $I->seeResponseIsSuccessful();
        $response = $I->grabResponse();
        $data = json_decode($response, true);

        $I->assertTrue($data['url'] == $webhookName);
    }
}
