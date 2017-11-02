<?php
/**
 * Created by PhpStorm.
 * User: wamil
 * Date: 03-Nov-17
 * Time: 00:59
 */

namespace App\Http\Controllers;


use App\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function create(Request $request)
    {
        if ($request->has('name') && $request->has('price') && $request->has('duration'))
        {
            $package = new Package();

            $package->name = $request->get('name');
            $package->price = $request->get('price');
            $package->duration = $request->get('duration');
            if ($request->has('discount_price'))
            {
                $package->discount_price = $request->get('discount_price');
            }
            $package->save();
            $result = ['status' => 200];
        }
        else
        {
            $result = ['status' => 406];
        }

        return response($result);
    }

    public function update(Request $request)
    {
        $package = Package::find($request->get('id'));

        if ($package)
        {
            if ($request->has('name'))
            {
                $package->name = $request->get('name');

            }

            if ($request->has('price'))
            {
                $package->price = $request->get('price');

            }

            if ($request->has('discount_price'))
            {
                $package->discount_price = $request->get('discount_price');

            }

            if ($request->has('duration'))
            {
                $package->duration = $request->get('duration');

            }

            $package->save();
            $result = ['status' => 200];
        }
        else
        {
            $result = ['status' => 408];
        }

        return response($result);
    }

    public function getPackages()
    {
        $packages = Package::paginate(10);
        $status = collect(['status' => 200]);
        $result = $status->merge($packages);
        return response($result);
    }

    public function get($id)
    {
        $package = Package::find($id);
        $result = ['status' => 200, 'data' => $package];
        return response($result);
    }

    public function delete($id)
    {
        $package = Package::find($id);
        $package->delete();
        $result = ['status' => 200];
        return response($result);
    }
}