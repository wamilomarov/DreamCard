<?php
/**
 * Created by PhpStorm.
 * User: wamil
 * Date: 26-Oct-17
 * Time: 03:24
 */

namespace App\Http\Controllers;


use App\Campaign;
use App\Card;
use App\Helper;
use App\Partner;
use App\Photo;
use App\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class PartnerController extends Controller
{
    public function create(Request $request)
    {
        if ($request->has('name') && $request->has('category_id') && $request->hasFile('photo')
            && $request->has('username') && $request->has('password')) {
            if (Partner::arrangeUser()->where('username', $request->get('username'))->exists()) {
                $result = ['status' => 413];
                return response()->json($result);
            }
            $partner = new Partner();

            $photo = new Photo();
            $photo_result = $photo->upload($request->file('photo'), 'uploads/photos/partners/');

            if ($photo_result == 200) {
                $partner->name = $request->get('name');
                $partner->photo_id = $photo->id;
                $partner->category_id = $request->get('category_id');
                $partner->username = $request->get('username');
                $partner->password = app('hash')->make($request->get('password'));
                $partner->save();
                $result = ['status' => 200];
            } else {
                $result = ['status' => $photo_result];
            }
        } else {
            $result = ['status' => 406];
        }
        return response($result);
    }

    public function update(Request $request)
    {
        $partner = Partner::arrangeUser()->find($request->get('id'));

        if ($partner && $partner->isEditableByGuard()) {
            if ($request->has('name')) {
                $partner->name = $request->get('name');

            }

            if ($request->has('category_id')) {
                $partner->category_id = $request->get('category_id');

            }

            if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
                $photo = new Photo();
                $photo_result = $photo->upload($request->file('photo'), 'uploads/photos/partners/');

                if ($photo_result == 200) {
                    $partner->deletePhoto();
                    $partner->photo_id = $photo->id;
                } else {
                    return response(['status' => $photo_result]);
                }
            }

            if ($request->has('username')) {
                $partner->username = $request->get('username');
            }

            if ($request->has('password') && $request->has('prev_password')) {
                if ($partner && Hash::check($request->get('prev_password'), $partner->getAuthPassword())) {
                    $partner->password = app('hash')->make($request->get('password'));
                    $partner->first_entry = 0;
                    $partner->api_token = null; //  log out when password is changed
                } else {
                    return response(['status' => 409]);
                }
            }

            $partner->save();
            $result = ['status' => 200];
        } else {
            $result = ['status' => 408];
        }

        return response($result);
    }

    public function getPartners(Request $request)
    {
        $partners = Partner::arrangeUser()->paginate(10);
        $status = collect(['status' => 200]);
        $partners->appends($request->all())->render();
        $result = $status->merge($partners);
        return response($result);
    }

    public function getCampaigns(Request $request, $id)
    {
        if (Auth::user()!= null && Auth::user()->getTable() == 'users')
        {
            $campaigns = Campaign::arrangeUser()->where('partner_id', $id)->where('end_date', '>', Date("Y-m-d H:i:s"))->paginate(10);
        }
        else
        {
            $campaigns = Campaign::arrangeUser()->where('partner_id', $id)->orderBy('deleted_at')->paginate(10);

        }
        $campaigns->appends($request->all())->render();
        $status = collect(['status' => 200]);
        $result = $status->merge($campaigns);
        $result->put('partner', Partner::withTrashed()->find($id));

//        echo Helper::sendPushNotification(['7336c5a2-3f59-4b17-b4e8-c4fb93227406'], "yeni xeber", "wecw", 0, 'f');

        return response($result);
    }

    public function get($id)
    {
        $partner = Partner::arrangeUser()->find($id);
        $result = ['status' => 200, 'data' => $partner];
        return response($result);
    }

    public function delete($id)
    {
        $partner = Partner::arrangeUser()->find($id);
        $partner->photo != null ? $partner->photo->remove() : $a = "";
        $partner->forceDelete();
        $result = ['status' => 200];
        return response($result);
    }

    public function disable($id)
    {
        $partner = Partner::arrangeUser()->find($id);
        $partner->delete();
        $result = ['status' => 200];
        return response($result);
    }

    public function restore($id)
    {
        $partner = Partner::arrangeUser()->find($id);
        $partner->restore();
        $result = ['status' => 200];
        return response($result);
    }

    public function rate(Request $request)
    {
        if ($request->has('partner_id') && $request->has('rate'))
        {
            if (DB::table('ratings')->where('partner_id', $request->get('partner_id'))
                        ->where('user_id', Auth::user()->id)
                        ->exists())
            {
                DB::table('ratings')
                    ->where('partner_id', $request->get('partner_id'))
                    ->where('user_id', Auth::user()->id)
                    ->update(
                        [
                            'rate' => $request->get('rate')
                        ]
                    );
            }
            else
            {
                DB::table('ratings')
                    ->insert(
                        [
                            'partner_id' => $request->get('partner_id'),
                            'user_id' => Auth::user()->id,
                            'rate' => $request->get('rate')
                        ]
                    );
            }

            $result = ['status' => 200];

        }
        else
        {
            $result = ['status' => 406];
        }
        return response()->json($result);
    }

    public function approvePurchase(Request $request)
    {
        if ($request->has('qr_code') && $request->has('campaign_id'))
        {
            if (Card::where('qr_code', $request->get('qr_code'))->exists() &&
                Campaign::where('id', $request->get('campaign_id'))->exists())
            {
                $card = Card::where('qr_code', $request->get('qr_code'))->first();
                if ($card->qr_code == null)
                {
                    $result = ['status' => 415];
                }
                else
                {
                    $diff = 120 - (strtotime(date('Y-m-d H:i:s')) - strtotime($card->qr_created_at));
                    if ($diff <= 0)
                    {
                        $result = ['status' => 415];
                    }
                    else
                    {
                        $purchase = new Purchase();
                        $purchase->card_id = $card->id;
                        $purchase->campaign_id = $request->get('campaign_id');
                        $purchase->save();
                        $result = ['status' => 200];
                    }
                }
            }
            else
            {
                $result = ['status' => 408];
            }
        }
        else
        {
            $result = ['status' => 406];
        }

        return response()->json($result);
    }

}