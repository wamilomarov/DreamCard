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
//        $request = $request->json();
        if ($request->has('name') && $request->has('price') && $request->has('duration'))
        {
            if (Package::where('name', $request->get('name'))->exists())
            {
                return response()->json(['status' => 407]);
            }
            $package = new Package();

            $package->name = $request->get('name');
            $package->price = $request->get('price');
            $package->duration = $request->get('duration');
            if ($request->has('discount_price') && $request->get('discount_price') != 0 && $request->get('discount_price') != null)
            {
                $package->discount_price = $request->get('discount_price');
            }
            else
            {
                $package->discount_price = $request->get('price');
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
        $package = Package::arrangeUser()->find($request->get('id'));

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

            if ($request->has('discount_price') && $request->get('discount_price') != 0 && $request->get('discount_price') != null)
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
        $packages = Package::arrangeUser()->paginate(10);
        $status = collect(['status' => 200]);
        $result = $status->merge($packages);
        return response($result);
    }

    public function get($id)
    {
        $package = Package::arrangeUser()->find($id);
        $result = ['status' => 200, 'data' => $package];
        return response($result);
    }

    public function delete($id)
    {
        $package = Package::arrangeUser()->find($id);
        $package->delete();
        $result = ['status' => 200];
        return response($result);
    }

    public function disable($id)
    {
        $category = Package::arrangeUser()->find($id);
        $category->delete();
        $result = ['status' => 200];
        return response($result);
    }

    public function restore($id)
    {
        $package = Package::arrangeUser()->find($id);
        $package->restore();
        $result = ['status' => 200];
        return response($result);
    }
}