<?php
/**
 * Created by PhpStorm.
 * User: wamil
 * Date: 23-Oct-17
 * Time: 04:00
 */

namespace App\Http\Controllers;


use App\Card;
use App\Photo;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function create(Request $request)
    {
        $request = $request->json();
        if ($request->has('email') && $request->has('phone') && $request->has('password')) {
            if (User::where('email', $request->get('email'))
                ->orWhere('phone', $request->get('phone'))->exists()) {
                $result = ['status' => 407];
            } else {
                $user = new User();
                $user->email = $request->get('email');
                $user->phone = $request->get('phone');
                $user->password = app('hash')->make($request->get('password'));
                $user->photo_id = NULL;

                $user->api_token = md5(microtime());
                $user->save();
                $user = $user->makeVisible(['api_token']);
                $result = ['status' => 200, 'data' =>  $user];
            }

        }
        else {
            $result = ['status' => 406];
        }

        return response($result);
    }

    public function login(Request $request)
    {
        $request = $request->json();
        if ($request->has('email') && $request->has('password')) {
            $user = User::where('email', $request->get('email'))->first();

            if ($user && Hash::check($request->get('password'), $user->getAuthPassword())) {
                $user->api_token = md5(microtime());
                $user->save();
                $user = $user->makeVisible(['api_token']);
                $result = ['status' => 200, 'data' => $user];
            } else {
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
        $user = Auth::user();
        $user->api_token = null;
        $user->save();
        $result = ['status' => 200];
        return response($result);
    }

    public function update(Request $request)
    {
        $request = $request->json();
        $user = User::find($request->get('id'));
        if ($user) {
            if ($request->has('first_name')) {
                $user->first_name = $request->get('first_name');
            }

            if ($request->has('last_name')) {
                $user->last_name = $request->get('last_name');
            }

            if ($request->has('email')) {
                $user->email = $request->get('email');
            }

            if ($request->has('phone')) {
                $user->phone = $request->get('phone');
            }

            if ($request->has('password') && $request->has('prev_password')) {
                if ($user && Hash::check($request->get('prev_password'), $user->getAuthPassword())) {
                    $user->password = app('hash')->make($request->get('password'));
                    $user->api_token = null;  //  log out when password is changed
                } else {
                    return response(['status' => 409]);
                }
            }

            if ($request->has('city_id')) {
                $user->city_id = $request->get('city_id');
            }

            if ($request->hasFile('photo')) {
                $photo = new Photo();
                $photo_result = $photo->upload($request->file('photo'), 'uploads/photos/users/');

                if ($photo_result == 200) {
                    $user->deletePhoto();
                    $user->photo_id = $photo->id;
                } else {
                    return response(['status' => $photo_result]);
                }
            }

            $user->save();
            $result = ['status' => 200];
        } else {
            $result = ['status' => 408];
        }

        return response($result);
    }

    public function get($id)
    {
        $user = User::find($id);

        $result = ['status' => 200, 'data' => $user];

        return response($result);
    }

    public function getUsers()
    {
        $users = User::paginate(10);
        $status = collect(['status' => 200]);
        $result = $status->merge($users);
        return response($result);
    }

    public function delete($id)
    {
        $user = User::find($id);
        $user->deletePhoto();
        $user->delete();
        $result = ['status' => 200];

        return response($result);
    }

    public function forgotPassword(Request $request)
    {
        $request = $request->json();
        if ($request->has('email'))
        {
            if (User::where('email', $request->get('email'))->where('status', 2)->exists())
            {
                $token = crypt(sha1(microtime()), 'password_reset');
                DB::table('password_resets')->insert([
                    'email' => $request->get('email'),
                    'token' => $token
                ]);
                $url = url() . "/users/reset_password?token=$token";
                $subject = "Password Reset";
                $to = $request->get('email');
                $message = "$url";
                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

                $headers .= 'From: <dreamcard@dreamcard.az>' . "\r\n";
//                $headers .= 'Cc: admin@dreamcard.az' . "\r\n";

                if (mail($to, $subject, $message, $headers))
                {
                    $result = ['status' => 200];
                }
                else
                {
                    $result = ['status' => 412];
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

        return response($result);
    }

    public function resetPassword(Request $request)
    {
        $request = $request->json();
        if ($request->has('token') && $request->has('password'))
        {
            $token = $request->get('token');
            $password = $request->get('password');

            $password_reset = DB::table('password_resets')->where('token', $token)->first();
            $email = $password_reset->email;

            $user = User::where('email', $email)->first();
            $user->password = app('hash')->make($password);
            $user->save();

            $result = ['status' => 200];
        }
        else
        {
            $result = ['status' => 406];
        }

        return response($result);
    }

//              PAYMENT
    public function pay(Request $request)
    {
        $request = $request->json();
        $this->validate($request, [
            'cardType' => ['regex:/^[v|m]$/'],
            'amount' => ['regex:/^[0-9.]*$/'],
            'item' => ['regex:/^[a-zA-Z0-9]*$/'],
            'lang' => ['regex:/^(lv|en|ru)$/'],
        ]);

        $cardType = $request->get('cardType');
        $amount = intval($request->get('amount')) * 100;
        $description = $request->get('item');
        $lang = $request->get('lang');

        $stub = new PaymentController();

        /*
         * Response: {"status":{"code":1,"message":"success"},"paymentKey":"8d53b07f-ec45-48b9-b877-c0e9d5c54682"}
         *
         * Save payment key to your db : $resp->paymentKey
         */
        $resp = $stub->getPaymentKeyJSONRequest($amount, $lang, $cardType, $description);
//        var_dump($resp); exit;
//        DB::table('payments')->insert([
//            'payment_key' => $resp->paymentKey,
//            'payment_source' => 'golden_pay'
//        ]);
        return redirect($resp->urlRedirect);
    }

    public function getPaymentForm()
    {
        return
            '
        <form action="http://dreamcard.az/api/pay" method="post">
    Amount : <input type="text" name="amount" value="" /> <br>
    Item : <input type="text" name="item" value="" /> <br>
    Card type : 
    <select name="cardType">
        <option value=\'v\'>Visa</option>
        <option value=\'m\'>Master</option>
    </select> <br>
    
    Lang : 
    <select name="lang">
        <option value=\'lv\'>Az</option>
        <option value=\'ru\'>Ru</option>
        <option value=\'ru\'>En</option>
    </select> <br>
    <input type="submit" name="selectItem" value="Select item">
</form>
        ';
    }

    public function paymentCallback(Request $request)
    {
        $request = $request->json();
        $this->validate($request, [
            'payment_key' => 'regex:/^[a-zA-Z0-9\-]*$/'
        ]);
        $payment_key = $request->get('payment_key');
        $stub = new PaymentController();
        $resp = $stub->getPaymentResult($payment_key);


        if ($resp->status->code == 1 && $resp->checkCount == 0) {
            echo "Payment was successful";
            echo "<br>amount: " . $resp->amount;
            echo "<br>amount: " . $resp->paymentDate;
        } else {
            echo "Payment was <b>unsuccessful</b>";
        }
    }

    public function paymentError()
    {
        return url();
    }

    public function millionCheckId(Request $request)
    {
        $request = $request->json();
        $id = $request->get('id');
        $card = Card::find($id);
        if ($card) {
            $result = "
        <response>
          <id>$card->id</id>
          <balance>$card->balance AZN</balance>
          <code>0</code>
          <message>OK</message>
        </response>
        ";
        } else {
            $result = "
        <response>
          <code>1</code>
          <code>Wrong ID specified</code>
        </response>
        ";
        }

        return response($result)->header('Content-Type', 'text/xml');
    }

    public function millionPay(Request $request)
    {
        $request = $request->json();
        $id = $request->get('id');
        $amount = $request->get('amount');
        $currency = $request->get('currency');
        $guid = $request->get('guid');

        if (DB::table('payments')->where('payment_source', 'million')
                ->where('payment_key', $guid)->count() > 0) {
            $result = "
            <response>
              <code>2</code>
              <code>Duplicate GUID specified</code>
            </response>";
        } else {
            $insert = DB::table('payments')->insert([
                'payment_source' => 'million',
                'payment_key' => $guid,
                'amount' => $amount,
                'card_id' => $id
            ]);
            $card = Card::find($id);

            if ($insert && $card) {


                $card->balance += $amount;
                $card->save();
                $result = "
                <response>
                  <balance>$card->balance AZN</balance>
                  <code>0</code>
                  <code>OK</code>
                </response>";

            } else {
                $result = "
                <response>
                  <code>3</code>
                  <code>Unknown reason</code>
                </response>";
            }
        }

        return response($result)->header('Content-Type', 'text/xml');
    }
}