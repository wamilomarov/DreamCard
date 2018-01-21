<?php
/**
 * Created by PhpStorm.
 * User: Lenova
 * Date: 1/5/2018
 * Time: 22:41
 */

namespace App\Http\Controllers;

use App\Partner;
use App\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Campaign;


class CampaignController extends Controller
{
  public function create(Request $request){

    if($request->has('title') && $request->has('department_id') && $request->has('partner_id') && $request->hasFile('photo')
      && $request->has('all_products_discount') && $request->has('special_product_discount') && $request->has('end_date')
    ){
      if(Campaign::arrangeUser()->where('department_id', $request->has('department_id'))->where('partner_id', $request->has('partner_id'))->exists()){
        $result = ['status' => 414];
        return response()->json($result);
      }

      $photo = new Photo();
      $photo_result = $photo->upload($request->file('photo'), 'uploads/photos/campaigns/');
      if ($photo_result != 200)
      {
          $result = ['status' => $photo_result];
          return response()->json($result);
      }

      $campaign = new Campaign();
      $campaign->title = $request->get('title');
      $campaign->department_id = $request->get('department_id');
      $campaign->partner_id = $request->get('partner_id');
      $campaign->photo_id = $photo->id;
      $campaign->all_products_discount = $request->get('all_products_discount');
      $campaign->special_product_discount = $request->get('special_product_discount');
      $campaign->end_date = $request->get('end_date');
      $campaign->save();
      $result = ['status' => 200];

    }else{
      $result = ['status' => 406];
    }

    return response($result);

  }

  public function getCampaign(){
//    $campaigns = Campaign::arrangeUser()->with('partner')->orderBy('created_at', 'desc')->paginate(10);
//    $data = ['data' => $campaigns];
    $campaigns = Partner::arrangeUser()->with('campaign')->orderBy('created_at', 'desc')->paginate(10);
    $status = collect(['status' => 200]);
    $result = $status->merge($campaigns);

    return response($result);
  }

  public function get($id){
    $campaign = Campaign::arrangeUser()->find($id);
    $result = ['status' => 200, 'data' => $campaign];
    return response($result);
  }

  public function update(Request $request){
    $campaign = Campaign::arrangeUser()->find($request->get('id'));

    if($campaign){
      if($request->has('title')){
        $campaign->title = $request->get('title');
      }
      if($request->has('department_id')){
        $campaign->department_id = $request->get('department_id');
      }
      if($request->has('partner_id')){
        $campaign->partner_id = $request->get('partner_id');
      }
      if($request->has('all_products_discount')){
        $campaign->all_products_discount = $request->get('all_products_discount');
      }
      if($request->has('special_product_discount')){
        $campaign->special_product_discount = $request->get('special_product_discount');
      }
      if($request->has('end_date')){
        $campaign->end_date = $request->get('end_date');
      }

      $campaign->save();
      $result = ['status' => 200];
    }else{
      $result = ['status' => 408];
    }

    return response($result);
  }

  public function delete($id){
    $campaign = Campaign::arrangeUser()->find($id);
    $campaign->forceDelete();

    $result = ['status' => 200];
    return response($result);
  }



}