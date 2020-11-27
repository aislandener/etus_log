<?php

namespace Etus\EtusLog;

use BaoPham\DynamoDb\DynamoDbModel;
use Illuminate\Support\Facades\Validator;
use stdClass;
use Ramsey\Uuid\Uuid;
use Carbon\Carbon;

/**
 * Class Log
 * @package Etus\EtusLog
 *
 * @deprecated
 *
 * deprecated in 1.6 - 2020-11-27 14:24:00 UTC
 */
class Log extends DynamoDbModel{

    protected $table;
    protected $fillable = ["origem", "primary_key", "request", "response"];
    public $timestamps = false;

    protected $dynamoDbIndexKeys = array(
        'origem-index' => [
            'hash' => 'origem'
        ]
    );

    public function __construct(){

        $log_enable = env('LOG_ENABLE');

        if ($log_enable) {
            $env = [
                'DYNAMODB_KEY' => env('DYNAMODB_KEY'),
                'DYNAMODB_SECRET' => env('DYNAMODB_SECRET'),
                'DYNAMODB_REGION' => env('DYNAMODB_REGION'),
                'DYNAMODB_DEBUG' => env('DYNAMODB_DEBUG'),
                'ETUS_LOG_DYNAMO_TABLE_NAME' => env('ETUS_LOG_DYNAMO_TABLE_NAME')
            ];

            $validator = Validator::make($env, [
                'DYNAMODB_KEY' => 'required',
                'DYNAMODB_SECRET' => 'required',
                'DYNAMODB_REGION' => 'required',
                'DYNAMODB_DEBUG' => 'required',
                'ETUS_LOG_DYNAMO_TABLE_NAME' => 'required'
            ]);

            if ($validator->fails()) {
                throw new \Exception('Exception: ' . $validator->errors()->all()[0]);
            }

            $this->table = env('ETUS_LOG_DYNAMO_TABLE_NAME');
        }
    }

    public function store($data)
    {
        if (env('LOG_ENABLE')) {
            try {

                $validator = Validator::make($data, [
                    'origin' => 'required',
                    'request' => 'required',
                    'response' => 'required'
                ]);

                if ($validator->fails()) {
                    return $validator->errors()->all();
                }

                $data['request']    = json_encode($data['request']);

                if(is_array($data['response']) || is_object(($data['response']))) {
                    $data['response'] = json_encode($data['response']);
                }

                $now = Carbon::now();
                $timestamp = $now->timestamp;

                $log = new Log();
                $log->id = (string) Uuid::uuid4();
                $log->created_at = (int) $timestamp;
                $log->origin = (string) $data['origin'];

                if(isset($data['primary_key'])) {
                    $log->primary_key = $data['primary_key'];
                }

                $log->request = $data['request'];
                $log->response = $data['response'];

                $log = $log->save();

                if ($log) {

                    return true;

                } else {

                    return ['Error trying to save'];

                }

            } catch (\Exception $e) {
                return ['Internal server error ' . $e->getMessage()];
            }
        } else {
            return true;
        }
    }
}
