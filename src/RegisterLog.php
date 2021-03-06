<?php

namespace Etus\EtusLog;

use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Marshaler;
use Exception;

class RegisterLog{
    private $client;
    private $marshaler;
    private $table;

    public function __construct()
    {
        $this->client = new DynamoDbClient([
            'version' => config('etus_log.aws.version'),
            'region'  => config('etus_log.aws.region'),
            'credentials' => [
                'key'    => config("etus_log.aws.key_id"),
                'secret' => config("etus_log.aws.secret_key"),
            ],
        ]);
        $this->marshaler = new Marshaler();
        $this->table = config("etus_log.aws.table");
    }

    public function storeV2($data, $table = null){
        try {
            if (is_null($table)) {
                $table = $this->table;
            }

            if (!config('etus_log.enable')) {
                return (object) [
                    'success' => true
                ];
            }

            $data = (array)$data;

            $timestamp = time();

            $data['id'] = (string) uniqid() . "-" . uniqid() . "-" . time();
            $data['created_at'] = (int)$timestamp;

            $item = $this->marshaler->marshalItem($data);

            $params = [
                'TableName' => $table,
                'Item' => $item
            ];

            $result = $this->client->putItem($params);

            return (object) [
                'success' => true,
                'message' => $result
            ];
        }catch (Exception $e){
            return (object) [
                'success' => false,
                'message' => 'Internal server error ' . $e->getMessage()
            ];
        }
    }

    public function store($data, $table = null)
    {
        if(is_null($table)){
            $table = $this->table;
        }
        if(!config('etus_log.enable'))
            return (object)[
                'success' => true
            ];

        try {
            $data['request'] = json_encode($data['request']);

            if(is_array($data['response']) || is_object(($data['response']))) {
                $data['response'] = json_encode($data['response']);
            }

            $timestamp = time();

            $log = [
                'id' => (string) uniqid() . "-" . uniqid() . "-" . time(),
                'created_at' => (int) $timestamp,
                'origem' => (string) $data['origin'],
                'request' => $data['request'],
                'response' => $data['response']
            ];

            if(isset($data['primary_key'])) {
                $log['primary_key'] = $data['primary_key'];
            }

            $item = $this->marshaler->marshalItem($log);

            $params = [
                'TableName' => $table,
                'Item' => $item
            ];

            $result = $this->client->putItem($params);

            return (object)[
                'success' => true,
                'message' => $result
            ];

        }
        catch (Exception $e){
            return (object)[
                'success' => false,
                'message' => 'Internal server error ' . $e->getMessage()
            ];
        }


    }
}
