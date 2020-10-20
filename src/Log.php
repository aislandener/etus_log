<?php

namespace Etus\EtusLog;

use BaoPham\DynamoDb\DynamoDbModel;
use Illuminate\Support\Facades\Validator;
use stdClass;
use Ramsey\Uuid\Uuid;
use Carbon\Carbon;

class Log extends DynamoDbModel{
    
    protected $table = "logs_pacote";
    protected $fillable = ["origem", "primary_key", "request", "response"];
    public $timestamps = false;
    
    protected $dynamoDbIndexKeys = array(
        'origem-index' => [
            'hash' => 'origem'
        ]
    );

    public function __construct(){}

    public function store($data)
    {
        try {

            $validator = Validator::make($data, [
                'origem' => 'required',
                'primary_key' => 'required',
                'request' => 'required|array',
                'response' => 'required|array'
            ]);

            if ($validator->fails()) {
                return $validator->errors()->all();
            }
    
            $data['request']    = json_encode($data['request']);
            $data['response']   = json_encode($data['response']);
    
            $now = Carbon::now();
            $timestamp = $now->timestamp;

            $log = new Log();
            $log->id = (string) Uuid::uuid4();
            $log->created_at = $timestamp;
            $log->origem = $data['origem'];
            $log->primary_key = $data['primary_key'];
            $log->request = $data['request'];
            $log->response = $data['response'];

            $log = $log->save();

            if ($log) {

                return true;

            } else {
            
                return ['Error trying to save'];

            }
            
        } catch (\Exception $e) {

            return ['Internal server error'];

        }
    }
}
