<?php
/**
 * Created by PhpStorm.
 * User: wamil
 * Date: 24-Oct-17
 * Time: 01:52
 */

namespace App\Http\Controllers;


use App\Category;
use App\Partner;
use App\Photo;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
        public function create(Request $request)
    {
        if ($request->has('name') /*&& $request->hasFile('small_icon')*/ && $request->hasFile('large_icon'))
        {
            if (!Category::arrangeUser()->where('name', $request->get('name'))->exists())
            {
//                $small_icon = new Photo();
//                $small_icon_result = $small_icon->upload($request->file('small_icon'), 'uploads/photos/categories/');

                $large_icon = new Photo();
                $large_icon_result = $large_icon->upload($request->file('large_icon'), 'uploads/photos/categories/');

                if (/*$small_icon_result*/ 200 == 200)
                {
                    if ($large_icon_result == 200)
                    {
                        $category = new Category();
                        $category->name = $request->get('name');
                        $category->small_icon_id = /*$small_icon->id;*/ $large_icon->id;
                        $category->large_icon_id = $large_icon->id;

                        if ($request->has('order_by'))
                        {
                            $category->order_by = $request->get('order_by');
                        }

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
                    $result = ['status' => 400 /*$small_icon_result*/];
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

    public function get($id)
    {
        $category = Category::arrangeUser()->find($id);
        $result = ['status' => 200, 'data' => $category];
        return response($result);
    }

    public function getCategories()
    {
        $categories = Category::arrangeUser()->paginate(10);
        $status = collect(['status' => 200]);
        $result = $status->merge($categories);
        return response($result);
    }

    public function getPartners($id)
    {
        $categories = Partner::arrangeUser()->where('category_id', $id)->paginate(10);
        $status = collect(['status' => 200]);
        $result = $status->merge($categories);
        return response($result);
    }

    public function delete($id)
    {
        $category = Category::arrangeUser()->find($id);
//        $category->large_icon->remove();
        $category->small_icon->remove();
        $category->forceDelete();
        $result = ['status' => 200];
        return response($result);
    }

    public function update(Request $request)
    {
        $category = Category::arrangeUser()->find($request->get('id'));
        if ($category)
        {
            if ($request->has('name'))
            {
                $category->name = $request->get('name');
            }

            if ($request->hasFile('small_icon'))
            {
                $small_icon = new Photo();
                $small_icon_result = $small_icon->upload($request->file('small_icon'), 'uploads/photos/categories/');

                if ($small_icon_result == 200)
                {
                    $category->deleteSmallIcon();
                    $category->small_icon_id = $small_icon->id;
                }
                else
                {
                    return response(['status' => $small_icon_result]);
                }
            }

            if ($request->hasFile('large_icon'))
            {
                $large_icon = new Photo();
                $large_icon_result = $large_icon->upload($request->file('large_icon'), 'uploads/photos/categories/');

                if ($large_icon_result == 200)
                {
                    $category->large_icon->remove();
                    $category->large_icon_id = $large_icon->id;
                }
                else
                {
                    return response(['status' => $large_icon_result]);
                }
            }

            if ($request->has('order_by'))
            {
                $category->order_by = $request->get('order_by');
            }

            $category->save();
            $result = ['status' => 200];
        }
        else
        {
            $result = ['status' => 408];
        }

        return response($result);

    }

    public function disable($id)
    {
        $category = Category::arrangeUser()->find($id);
        $category->delete();
        $result = ['status' => 200];
        return response($result);
    }

    public function restore($id)
    {
        $category = Category::arrangeUser()->find($id);
        $category->restore();
        $result = ['status' => 200];
        return response($result);
    }
}