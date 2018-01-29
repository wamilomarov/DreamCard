<?php
/**
 * Created by PhpStorm.
 * User: wamil
 * Date: 03-Nov-17
 * Time: 01:19
 */

namespace App\Http\Controllers;


use App\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function create(Request $request)
    {
        $request = $request->json();
        if ($request->has('username') && $request->has('email') && $request->has('phone') && $request->has('password'))
        {
            if (Admin::where('email', $request->get('email'))
                ->orWhere('username', $request->get('username'))
                ->orWhere('phone', $request->get('phone'))->exists())
            {
                $result = ['status' => 407];
            }
            else
            {
                $admin = new Admin();
                $admin->username = $request->get('username');
                $admin->email = $request->get('email');
                $admin->password = app('hash')->make($request->get('password'));
                $admin->phone = $request->get('phone');
                $admin->save();
                $result = ['status' => 200, 'data' => $admin];
            }

        }
        else
        {
            $result = ['status' => 406];
        }

        return response()->json($result);
    }

    public function login(Request $request)
    {
        $request = $request->json();
        if ($request->has('email') && $request->has('password'))
        {
            $admin = Admin::where('email', $request->get('email'))->first();

            if($admin && Hash::check($request->get('password'), $admin->getAuthPassword()))
            {
                $admin->api_token = md5(microtime());
                $admin->save();
                $admin = $admin->makeVisible(['api_token']);
                $result = ['status' => 200, 'data' => $admin];
            }
            else
            {
                $result = ['status' => 401];
            }

        }
        else
        {
            $result = ['status' => 406];
        }

        return response($result);
    }

    public function logout()
    {
        $admin = Auth::user();
        $admin->api_token = null;
        $admin->save();
        $result = ['status' => 200];
        return response($result);
    }

    public function update(Request $request)
    {
        $request = $request->json();
        $admin = Admin::find($request->get('id'));
        if ($admin && $admin->isEditableByGuard())
        {
            if ($request->has('email'))
            {
                $admin->email = $request->get('email');
            }

            if ($request->has('phone'))
            {
                $admin->email = $request->get('phone');
            }

            if ($request->has('username'))
            {
                $admin->username = $request->get('username');
            }

            if ($request->has('password') && $request->has('prev_password'))
            {
                if ($admin && Hash::check($request->get('prev_password'), $admin->getAuthPassword()))
                {
                    $admin->password = app('hash')->make($request->get('password'));
                    $admin->api_token = null;  //  log out when password is changed
                }
                else
                {
                    return response(['status' => 409]);
                }
            }

            $admin->save();
            $result = ['status' => 200];
        }
        else
        {
            $result = ['status' => 408];
        }

        return response()->json($result);
    }

    public function get($id)
    {
        $admin = Admin::find($id);

        $result = ['status' => 200, 'data' => ['user' => $admin]];

        return response()->json($result);
    }

    public function getAdmins()
    {
        $admins = Admin::paginate(10);
        $status = collect(['status' => 200]);
        $result = $status->merge($admins);
        return response()->json($result);
    }

    public function delete($id)
    {
        $admin = Admin::find($id);
        $admin->forceDelete();
        $result = ['status' => 200];
        return response()->json($result);

    }
  public function disable($id)
  {
      $admin = Admin::find($id);
      $admin->delete();
      $result = ['status' => 200];
      return response($result);
  }

    public function restore($id)
    {
        $admin = Admin::find($id);
        $admin->restore();
        $result = ['status' => 200];
        return response($result);
    }

    public function getFaq($id){
      $faq = DB::table('faq')->where('id', $id)->first();
      $result = ['status' => 200, 'data' => $faq];
      return response($result);
    }

    public function faqCreate(Request $request){
      $request = $request->json();
      if(
        $request->has('question_en') &&
        $request->has('question_az') &&
        $request->has('question_ru') &&
        $request->has('answer_ru') &&
        $request->has('answer_en') &&
        $request->has('answer_az')
      ){

        DB::table('faq')->insert([
          'question_en' => $request->get('question_en'),
          'question_ru' => $request->get('question_ru'),
          'question_az' => $request->get('question_az'),
          'answer_en' => $request->get('answer_en'),
          'answer_ru' => $request->get('answer_ru'),
          'answer_az' => $request->get('answer_az'),
        ]);

        $result = ['status' => 200];

      }else{
        $result = ['status' => 406];
      }

      return response($result);
    }

    public function faqUpdate(Request $request){

      $request = $request->json();

      $faq = DB::table('faq')->where('id', $request->get('id'))->get();

      if($faq){

        if($request->has('question_en')){
          $faq->question_en = $request->get('question_en');
        }

        if($request->has('question_ru')){
          $faq->question_ru = $request->get('question_ru');
        }

        if($request->has('question_az')){
          $faq->question_az = $request->get('question_az');
        }

        if($request->answer('answer_az')){
          $faq->answer_az = $request->get('answer_az');
        }

        if($request->answer('answer_ru')){
          $faq->answer_ru = $request->get('answer_ru');
        }

        if($request->answer('answer_az')){
          $faq->answer_en = $request->get('answer_en');
        }

        $faq->save();
        $result = ['status' => 200];

      }else{
        $result = ['status' => 408];
      }

      return response($result);
    }

    public function faqDelete($id){

      $faq = DB::table('faq')->where('id', $id);
      $faq->delete();
      $result = ['status' => 200];
      return response($result);

    }
}