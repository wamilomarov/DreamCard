<?php
/**
 * Created by PhpStorm.
 * User: wamil
 * Date: 23-Oct-17
 * Time: 04:00
 */

namespace App\Http\Controllers;


use App\Card;
use App\Category;
use App\News;
use App\Photo;
use App\Purchase;
use App\User;
use App\Partner;
use Facebook\Facebook;
use Google_Client;
use Google_Service_Drive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function cities()
    {
        $cities = DB::table('cities')->select(['id', 'name'])->get();
        $result = ['status' => 200, 'data' => $cities];
        return response()->json($result);
    }

    public function create(Request $request)
    {
        $request = $request->json();
        if ($request->has('email') && $request->has('phone') && $request->has('password')
            && $request->has('first_name') && $request->has('last_name')) {
            if (User::where('email', $request->get('email'))
                ->orWhere('phone', $request->get('phone'))->exists()) {
                $result = ['status' => 407];
            } else {
                $user = new User();
                $user->email = $request->get('email');
                $user->phone = $request->get('phone');
                $user->first_name = $request->get('first_name');
                $user->last_name = $request->get('last_name');
                $user->password = app('hash')->make($request->get('password'));
                $user->photo_id = NULL;

                $user->api_token = md5(microtime());
                $user->save();
                $card = new Card();
                $card->user_id = $user->id;
                $card->generateNumber();
                $card->save();
                $user = User::with('card')->find($user->id);
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
            $user = User::where('email', $request->get('email'))->with('card')->first();

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

    public function fbLogin(Request $request){
      $fb = new Facebook([
        'app_id' => '126332011463258',
        'app_secret' => '2212a0100df3d8b1ac36a0af452b8191',
        'default_graph_version' => 'v2.10',
      ]);

      try {
        // Get the \Facebook\GraphNodes\GraphUser object for the current user.
        // If you provided a 'default_access_token', the '{access-token}' is optional.
        $response = $fb->get('/me?fields=name,email', $request->get('access_token'));
      } catch(\Facebook\Exceptions\FacebookResponseException $e) {
        // When Graph returns an error
//        echo 'Graph returned an error: ' . $e->getMessage();
        $result = ['status' => $e->getCode()];
        return response($result);
      } catch(\Facebook\Exceptions\FacebookSDKException $e) {
        // When validation fails or other local issues
//        echo 'Facebook SDK returned an error: ' . $e->getMessage();
        $result = ['status' => $e->getCode()];
        return response($result);
      }

      $me = $response->getGraphUser();

      list($first_name, $last_name) = explode(' ', $me->getName(), 2);
      $facebook_id = $me->getId();
      $email = $me->getEmail();

      if (User::where('facebook_id', $facebook_id)->exists())
      {
          $user = User::where('facebook_id', $facebook_id)->first();
          $user->api_token = md5(microtime());
          $user->save();
          $user = $user->makeVisible(['api_token']);
          $result = ['status' => 200, 'data' => $user];
      }
      elseif (User::where('email', $email)->exists())
      {
          $user = User::where('email', $email)->first();
          $user->facebook_id = $facebook_id;
          $user->api_token = md5(microtime());
          $user->save();
          $user = $user->makeVisible(['api_token']);
          $result = ['status' => 200, 'data' => $user];
      }
      else
      {
          $user = new User();
          $user->first_name = $first_name;
          $user->last_name = $last_name;
          $user->facebook_id = $facebook_id;
          $user->email = $email;
          if($user->save())
          {
              $card = new Card();
              $card->user_id = $user->id;
              $card->generateNumber();
              $card->save();
              $user = User::find($user->id);
              $user->api_token = md5(microtime());
              $user->save();
              $user = $user->makeVisible(['api_token']);
              $result = ['status' => 200, 'data' => $user];
          }
          else
          {
              $result = ['status' => 412];
          }
      }
      return response()->json($result);
    }
    public function googleLogin(Request $request){
        $client = new Google_Client(
            [
                'client_id' => "152790946469-o2g3fo0undot6764mpmggialtcebsaoo.apps.googleusercontent.com",
                'client_secret' => "__3yBatUA4J72k0jkAnZafG5"
            ]);
        $token = $client->fetchAccessTokenWithAuthCode($request->get('code'));
        var_dump($token); exit;
        $client->setAccessToken($token);

        $payload = $client->verifyIdToken($request->get('id_token'));
        if ($payload) {
            $userid = $payload['sub'];
            var_dump($userid); exit;
            // If request specified a G Suite domain:
            //$domain = $payload['hd'];
        } else {
            var_dump($client); exit;
            // Invalid ID token
        }


      list($first_name, $last_name) = explode(' ', $me->getName(), 2);
      $facebook_id = $me->getId();
      $email = $me->getEmail();

      if (User::where('facebook_id', $facebook_id)->exists())
      {
          $user = User::where('facebook_id', $facebook_id)->first();
          $user->api_token = md5(microtime());
          $user->save();
          $user = $user->makeVisible(['api_token']);
          $result = ['status' => 200, 'data' => $user];
      }
      elseif (User::where('email', $email)->exists())
      {
          $user = User::where('email', $email)->first();
          $user->facebook_id = $facebook_id;
          $user->api_token = md5(microtime());
          $user->save();
          $user = $user->makeVisible(['api_token']);
          $result = ['status' => 200, 'data' => $user];
      }
      else
      {
          $user = new User();
          $user->first_name = $first_name;
          $user->last_name = $last_name;
          $user->facebook_id = $facebook_id;
          $user->email = $email;
          if($user->save())
          {
              $card = new Card();
              $card->user_id = $user->id;
              $card->generateNumber();
              $card->save();
              $user = User::find($user->id);
              $user->api_token = md5(microtime());
              $user->save();
              $user = $user->makeVisible(['api_token']);
              $result = ['status' => 200, 'data' => $user];
          }
          else
          {
              $result = ['status' => 412];
          }
      }
      return response()->json($result);
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
//        $request = $request->json();
        $user = Auth::user();
        var_dump($request->file('photo'));exit;
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

            if ($request->has('firebase_id')) {
                $user->firebase_id = $request->get('firebase_id');
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

            if($request->has('language')){
              $user->language = $request->get('language');
            }

            if($request->has('notification_sound')){
              $user->notification_sound = $request->get('notification_sound');
            }

            if($request->has('notification')){
              $user->notification = $request->get('notification');
            }

            if($request->has('news')){
              $user->news = $request->get('news');
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
            $result = ['status' => 200, 'data' => $user];
        } else {
            $result = ['status' => 408];
        }

        return response($result);
    }

    public function purchasesHistory(Request $request)
    {
        /*$purchases = DB::table('purchases')
        ->leftJoin('departments', 'departments.id', '=', 'purchases.department_id')
        ->leftJoin('partners', 'partners.id', '=', 'departments.partner_id')
        ->leftJoin('campaigns', 'campaigns.partner_id', '=', 'partners.id')
        ->where('purchases.card_id', Auth::user()->card->id)
        ->select('purchases.id', 'purchases.created_at', 'purchases.discount', 'partners.name');*/

        $purchases = Purchase::where('card_id', Auth::user()->card->id);
        if ($request->has('from_date'))
        {
            $purchases = $purchases->where('purchases.created_at', '>', $request->get('from_date'));
        }
        if ($request->has('to_date'))
        {
            $purchases = $purchases->where('purchases.created_at', '<', $request->get('to_date'));
        }
        if ($request->has('category_id'))
        {
            $purchases = $purchases->where('partners.category_id', $request->get('category_id'));
        }

        $purchases = $purchases->latest()->paginate(15);
        $status = collect(['status' => 200]);
        $result = $status->merge($purchases);
        return response()->json($result);
    }

    public function get($id)
    {
        $user = User::with('card')->find($id);

        $result = ['status' => 200, 'data' => $user];

        return response($result);
    }

    public function getUsers()
    {
        $users = User::withTrashed()->paginate(10);
        $status = collect(['status' => 200]);
        $result = $status->merge($users);
        return response($result);
    }

    public function delete($id)
    {
        $user = User::withTrashed()->find($id);
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
            if (User::where('email', $request->get('email'))->exists())
            {
                $token = crypt(sha1(microtime()), 'password_reset');
                DB::table('password_resets')->insert([
                    'email' => $request->get('email'),
                    'token' => $token,
                    'created_at' => DB::raw('NOW()'),
                    'updated_at' => DB::raw('NOW()')
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
//        $request = $request->json();
//        var_dump($request->all());
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
//        $request = $request->json();
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
            echo "<p>Payment was <b>unsuccessful</b></p>";
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

    public function categories()
    {
        $categories = Category::all();
        $result = ['status' => 200, 'data' => $categories];
        return response()->json($result);
    }

    public function favoriteCategories()
    {
        $favorites = DB::table("partners")
        ->join("favorites", "favorites.partner_id", "=", "partners.id")
            ->where("favorites.user_id", Auth::user()->id)
                ->pluck("partners.category_id")->toArray();
        $categories = Category::whereIn("id", $favorites)->paginate();
        $status = collect(['status' => 200]);
        $result = $status->merge($categories);
        return response($result);
    }

    public function favoritePartners($category_id)
    {
      $favorites = Auth::user()->favoritePartners($category_id)
          ->with(['lastNews'])->paginate(10);
      $status = collect(['status' => 200]);
      $result = $status->merge($favorites);
      return response($result);
    }

    public function addFavoritePartner(Request $request)
    {
//        $request = $request->json();
        if($request->has('partner_id')){

          DB::table('favorites')->insert([
            'user_id' => Auth::user()->id,
            'partner_id' => $request->get('partner_id'),
            'updated_at' => DB::raw("NOW()"),
            'created_at' => DB::raw("NOW()")
          ]);

          $result = ['status' => 200];

        }else{
          $result = ['status' => 413];
        }

        return response($result);
    }

  public function deleteFavoritePartner($partner_id){
    DB::table('favorites')->where('user_id', Auth::user()->id)->where('partner_id', $partner_id)->delete();
    $result = ['status' => 200];

    return response()->json($result);

  }



  public function search(Request $request){

    if($request->has('q') && $request->get('q') != '')
    {
        $q = $request->get('q');
        $news = News::arrangeUser()->with('partner')->where('title', 'like', "%$q%")->get();
        $partners = Partner::arrangeUser()->with('campaign')->where('name', 'like', "%$q%")->get();
        $operation_history = Purchase::where('card_id', Auth::user()->card->id)
//            ->where('department', 'LIKE', "$q")
            ->get();
        $result = [
            'status' => 200,
            'data' =>
                [
                'news' => $news,
                'partners' => $partners,
                'operation_history' => $operation_history
                ]
                  ];
      return response($result);
    }
    else
    {
        return response()->json(['status' => 406]);
    }


  }

  public function faq()
  {
      $language = Auth::user()->language;
      $faqs = DB::table('faq')->select("question_$language AS question", "answer_$language AS answer", "created_at" )->get();
      $result = ['status' => 200, 'data' => $faqs];


    return response($result);
  }

  public function cardGenerate(){
    $card = Card::where('user_id', Auth::user()->id)->first();
    $card->generateQrCode();
    $result = ['status' => 200, 'data' => $card];

    return response()->json($result);

  }

  public function card(){
    $card = Card::where('user_id', Auth::user()->id)->first();
    $result = ['status' => 200, 'data' => $card];

    return response($result);
  }

}