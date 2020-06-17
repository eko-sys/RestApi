<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Product;
use App\User;
use App\ApiRequest;
use App\History;
use Illuminate\Support\Facades\DB;

class ProductRequest extends Controller
{
	public function requestProductPulsa(Request $request)
	{
		$user = Auth('api')->user();
		$pulsa = new ApiRequest;

		$validator = Validator::make($request->all(),[
			'action' => 'required'
		]);

		$response = [
			'status' => false,
			'msg' => $validator->messages()
		];

		if( $validator->fails() ){
			return response()->json(['error' => $response], 403);

		}else{
			if($request->action != 'order' && $request->action != 'status'){
				$status = [
					'status' => false,
					'msg' => 'Unkown Action'
				];

				return response()->json(['error' => $status], 404);
			}

			if($request->action == 'order'){

				$validator = Validator::make($request->all(),[
					'code' => 'required',
					'target' => 'required',
				]);

				$response = [
					'status' => false,
					'msg' => $validator->messages()
				];

				if( $validator->fails() ){
					return response()->json(['error' => $response], 403);

				}else{
					$product = Product::where('product_code', $request->code)->get();
					if( count($product) > 0){

						if($user->saldo > $product[0]->price){
						$response = $pulsa->apiPulsa($request->code, $request->target, $request->action);
							if($response->result == true){

			                    $history = History::create([
								    'order_id' => $response->data->trxid,
								    'name' => $product[0]->product_name,
								    'code' => $product[0]->product_code,
								    'price' => $product[0]->price,
								    'type' => $product[0]->type,
								    'user_id' => $user->id,
								    'refund' => '1'
								]);

			    				$saldo = $user->saldo - $product[0]->price;
			    				User::where('id', $user->id)
							        ->update(['saldo' => $saldo]);

							    $status = [
							    	'status' => true,
							    	'msg' => 'Pembelian Sukses Order Id anda '.$response->data->trxid.'',
							    ];

							    return response()->json(['msg' => $status], 200);
							}else{
								return response()->json(['msg' => $response], 403);
							}
						}else{
							$status = [
								'status' => false,
								'msg' => 'Saldo Anda Tidak Mencukupi Untuk Melakukan Permintaan Ini'
							];
							return response()->json(['msg' => $status ], 403);
						}

					}else{
						$status = [
							'status' => false,
							'msg' => 'Produk Tidak Tersedia!'
						];

						return response()->json(['msg' => $status], 404);
					}
					
				}

			}

			if($request->action == 'status'){
				$validator = Validator::make($request->all(),[
					'trxid' => 'required'
				]);

				$response = [
					'status' => false,
					'msg' => $validator->messages()
				];

				if($validator->fails()){
					return response()->json(['error' => $response], 403);
				}else{
					return response()->json(['msg' => $pulsa->apiPulsa($request->trxid, null, $request->action)]);
				}

			}

		}
		
	}

	public function requestProductGame(Request $request)
	{
		$user = Auth('api')->user();
		$game = new ApiRequest;

		$validator = Validator::make($request->all(),[
			'action' => 'required'
		]);

		$response = [
			'status' => false,
			'msg' => $validator->messages()
		];

		if( $validator->fails() ){
			return response()->json(['error' => $response], 403);

		}else{
			if($request->action != 'order' && $request->action != 'status'){
				$status = [
					'status' => false,
					'msg' => 'Unkown Action'
				];

				return response()->json(['error' => $status], 404);
			}

			if($request->action == 'order'){

				$validator = Validator::make($request->all(),[
					'code' => 'required',
					'target' => 'required',
				]);

				$response = [
					'status' => false,
					'msg' => $validator->messages()
				];

				if( $validator->fails() ){
					return response()->json(['error' => $response], 403);

				}else{
					$product = DB::table('product_games')->where('code', '=', $request->code)->get();
					if( count($product) > 0){

						if($user->saldo > $product[0]->price){
						$response = $game->apiGame($request->code, $request->target, $request->action);
							if($response->result == true){
								
								$history = History::create([
								    'order_id' => $response->data->trxid,
								    'name' => $product[0]->product_name,
								    'code' => $product[0]->product_code,
								    'price' => $product[0]->price,
								    'type' => $product[0]->type,
								    'user_id' => $user->id,
								    'refund' => '1'
								]);

			    				$saldo = $user->sald - $product[0]->price;
			    				User::where('id', Auth::user()->id)
							        ->update(['saldo' => $saldo]);

							    $status = [
							    	'status' => true,
							    	'msg' => 'Pembelian Sukses Order Id anda '.$response->data->trxid.'',
							    ];

							    return response()->json(['msg' => $status], 200);
							}else{
								return response()->json(['msg' => $response], 403);
							}
						}else{
							$status = [
								'status' => false,
								'msg' => 'Saldo Anda Tidak Mencukupi Untuk Melakukan Permintaan Ini'
							];
							return response()->json(['msg' => $status ], 403);
						}

					}else{
						$status = [
							'status' => false,
							'msg' => 'Produk Tidak Tersedia!'
						];

						return response()->json(['msg' => $status]);
					}
					
				}

			}

			if($request->action == 'status'){
				$validator = Validator::make($request->all(),[
					'trxid' => 'required'
				]);

				$response = [
					'status' => false,
					'msg' => $validator->messages()
				];

				if($validator->fails()){
					return response()->json(['error' => $response], 403);
				}else{
					return response()->json(['msg' => $game->apiGame($request->trxid, null, $request->action)]);
				}
			}

		}
		
	}
}
