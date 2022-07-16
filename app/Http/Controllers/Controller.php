<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(title="Control Tower", version="0.1")
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
	 * Build custom pagination object from the request record
	 * @param \Illuminate\Database\Eloquent\Builder $query
	 * @param array $fields // list of table fields to select
	 * @return array
	 */
	public function paginate($query, $fields = []){
		$limit = request()->limit ?? 20;
		$page = request()->page ?? 1;
		$offset = (($page-1) * $limit);
		$total_records = $query->count();
		$records = $query->skip($offset)->take($limit)->get($fields);
		$records_count = count($records);
		$total_pages = ceil($total_records / $limit);
		$result = [
			"records" => $records,
			"total_records" => $total_records,
			"record_count" => $records_count,
			"total_page" => $total_pages,
		];
		return $result;
	}


	/**
	 * Return success Http response (200)
	 * @return \Illuminate\Http\Response
	 */
	public function respond($data){
		return $data;
	}

}
