<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestLog extends Model
{
    use HasFactory;

    
     /**
     * The table primary key field
     *
     * @var string
     */
	protected $primaryKey = 'id';
	


    /**
     * return list page fields of the model.
     * 
     * @return array
     */
	public static function listFields(){
		return [ 
			"request_logs.id as log_id",
            "request_type",
            "request_logs.action as requested_action",
            "status",
            "callsign",
            "request_logs.updated_at as date_created"

			
		];
	}
}
