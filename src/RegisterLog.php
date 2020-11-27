<?php

namespace Etus\EtusLog;

use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Marshaler;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

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

    public function store($data)
    {
        if(!config('etus_log.enable'))
            return (object)[
                'success' => true
            ];

        try {
            $validator = Validator::make($data, [
                'origin' => 'required',
                'request' => 'required',
                'response' => 'required'
            ]);

            if ($validator->fails()) {
                return (object)[
                    'success' => false,
                    'message' =>$validator->errors()->all()
                ];
            }

            $data['request']    = json_encode($data['request']);

            if(is_array($data['response']) || is_object(($data['response']))) {
                $data['response'] = json_encode($data['response']);
            }

            $now = Carbon::now();
            $timestamp = $now->timestamp;

            $log = [
                'id' => (string) Uuid::uuid4(),
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
                'TableName' => $this->table,
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
