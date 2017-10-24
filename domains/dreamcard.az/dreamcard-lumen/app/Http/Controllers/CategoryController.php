<?php
/**
 * Created by PhpStorm.
 * User: wamil
 * Date: 24-Oct-17
 * Time: 01:52
 */

namespace App\Http\Controllers;


use App\Category;
use App\Photo;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function create(Request $request)
    {
        if ($request->has('name') && $request->hasFile('small_icon') && $request->hasFile('large_icon'))
        {
            if (!Category::where('name', $request->get('name'))->exists())
            {
                $small_icon = new Photo();
                $small_icon_result = $small_icon->upload($request->file('small_icon'), 'uploads/photos/categories/', 50, 50);

                $large_icon = new Photo();
                $large_icon_result = $large_icon->upload($request->file('large_icon'), 'uploads/photos/categories/', 150, 150);

                if ($small_icon_result == 200)
                {
                    if ($large_icon_result == 200)
                    {
                        $category = new Category();
                        $category->name = $request->get('name');
                        $category->small_icon_id = $small_icon->id;
                        $category->large_icon_id = $large_icon->id;

                        if ($request->has('order_by'))
                        {
                            $category->order_by = $request->get('order_by');
                        }
//                        else
//                        {
//                            $last_category = Category::whereRaw('order_by = (select max(`order_by`) from categories)')->get();
//                            if ($last_category != null)
//                            $category->order_by = $last_category->order_by;
//                            else
//                            {
//                                $category->order_by = 1;
//                            }
//                        }

                        $category->save();
                        $result = ['status' => 200];
                    }
                    else
                    {
                        $result = ['status' => $large_icon_result];
                    }
                }
                else
                {
                    $result = ['status' => $small_icon_result];
                }
            }
            else
            {
                $result = ['status' => 407];
            }
        }
        else
        {
            $result = ['status' => 406];
        }

        return response($result);
    }

    public function get()
    {
        $categories = Category::paginate(10);
        $status = collect(['status' => 200]);
        $result = $status->merge($categories);
        return response($result);
    }
}