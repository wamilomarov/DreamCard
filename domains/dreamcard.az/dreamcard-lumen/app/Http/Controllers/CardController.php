<?php
/**
 * Created by PhpStorm.
 * User: wamil
 * Date: 28-Oct-17
 * Time: 04:17
 */

namespace App\Http\Controllers;


use App\Card;
use App\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CardController extends Controller
{
    public function create(Request $request)
    {
        $user = Auth::user();

        if (!Card::where('user_id', $user->id)->exists()) {
            $card = new Card();
            $card->user_id = $request->get('user_id');
            $card->generateNumber();
            $card->generateQrCode();
            $card->save();
            $result = ['status' => 200];

        } else {
            $result = ['status' => 410];
        }

        return response($result);
    }

    public function getCards()
    {
        $cards = Card::paginate(10);
        $status = collect(['status' => 200]);
        $result = $status->merge($cards);
        return response($result);
    }

    public function get($id)
    {
        $card = Card::find($id);
        $result = ['status' => 200, 'data' => $card];
        return response($result);
    }

    public function delete($id)
    {
        $card = Card::find($id);
        $card->delete();
        $result = ['status' => 200];
        return response($result);
    }

    public function upgrade(Request $request)
    {
        $card = Card::find($request->get('card_id'));
        $package = Package::find($request->get('package_id'));
        if ($package->discount_price == NULL)
        {
            $price = $package->price;
        }
        else
        {
            $price = $package->discount_price;
        }

        if ($card && $package)
        {
            if ($card->balance >= $price)
            {
                DB::table('card_upgrades')->insert([
                    'card_id' => $card->id,
                    'package_id' => $package->id,
                    'price' => $price,
                    'end_time' => Date('Y-m-d H:i:s', strtotime("+$package->duration days"))
                ]);

                $card->balance -= $price;
                $card->save();
                $result = ['status' => 200];
            }
            else
            {
                $result = ['status' => 411];
            }

        }
        else
        {
            $result = ['status' => 408];
        }

        return response($result);
    }
}