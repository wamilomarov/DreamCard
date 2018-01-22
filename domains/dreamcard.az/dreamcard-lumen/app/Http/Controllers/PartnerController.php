<?php
/**
 * Created by PhpStorm.
 * User: wamil
 * Date: 26-Oct-17
 * Time: 03:24
 */

namespace App\Http\Controllers;


use App\Campaign;
use App\Partner;
use App\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class PartnerController extends Controller
{
    public function create(Request $request)
    {
//        $request = $request->json();
        if ($request->has('name') && $request->has('category_id') && $request->hasFile('photo')
            && $request->has('username') && $request->has('password')) {
            if (Partner::arrangeUser()->where('username', $request->has('username'))->exists()) {
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

            if ($request->hasFile('photo')) {
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

    public function getPartners()
    {
        $partners = Partner::arrangeUser()->paginate(10);
        $status = collect(['status' => 200]);
        $result = $status->merge($partners);
        return response($result);
    }

    public function getCampaigns($id)
    {
        $campaigns = Campaign::arrangeUser()->where('partner_id', $id)->paginate(10);
        $status = collect(['status' => 200]);
        $result = $status->merge($campaigns);
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
        $partner->photo->remove();
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

}