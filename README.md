# Dialogflow PHP

Unofficial PHP library for V1 of the Dialogflow API.

## Install
```
composer require 0099ff/dialogflowphp
```


## Usage
```php
include "vendor/autoload.php";

use DialogflowPHP\Client;

$client = new Client('developer_access_token', 'session_id');
$response = $client->query("Hi Chatbot!");

echo $response->result->fulfillment->speech;
```
> Hello, puny human
```php
echo $response->result->score;
```
> 0.91000002622604

Agent responses can also be returned as a JSON string:
```php
$response = $client->query("Hi chatbot!", $return_as_json=true);
echo $response;
```
> { "id": "xxxx", "timestamp": "2018-05-05T09:52:25.905Z", "lang": "en", "result": { "source": "agent", "resolvedQuery": "Hi chatbot!", "action": "", "actionIncomplete": false, "parameters": {}, "contexts": [], "metadata": { "intentId": "xxxx", "webhookUsed": "false", "webhookForSlotFillingUsed": "false", "intentName": "Hey" }, "fulfillment": { "speech": "Hello, puny human", "messages": [ { "type": 0, "speech": "Hello, puny human" } ] }, "score": 0.9100000262260437 }, "status": { "code": 200, "errorType": "success" }, "sessionId": "session_id" }
