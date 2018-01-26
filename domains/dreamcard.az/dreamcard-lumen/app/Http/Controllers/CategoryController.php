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
        if ($request->has('name_az') && $request->has('name_en') && $request->has('name_ru') /*&& $request->hasFile('small_icon')*/ && $request->hasFile('large_icon'))
        {
            if (!Category::arrangeUser()->where('name_az', $request->get('name_az'))->where('name_ru', $request->get('name_ru'))->where('name_en', $request->get('name_en'))->exists())
            {
                $mimeType = $request->file('large_icon')->getMimeType();
                $small_icon = new Photo();
//                $small_icon_result = $small_icon->upload($request->file('small_icon'), 'uploads/photos/categories/');

                $large_icon = new Photo();
                $large_icon_result = $large_icon->upload($request->file('large_icon'), 'uploads/photos/categories/');

                if (/*$small_icon_result*/ 200 == 200)
                {
                    if ($large_icon_result == 200)
                    {
                        $small_icon->url = $large_icon->resize($mimeType, 100, 100, 'uploads/photos/categories/');
                        $small_icon->save();
//                        var_dump($large_icon->id);
//                        var_dump($small_icon->id);
//                        exit;
                        $category = new Category();
                        $category->name_az = $request->get('name_az');
                        $category->name_ru = $request->get('name_ru');
                        $category->name_en = $request->get('name_en');
                        $category->small_icon_id = $small_icon->id;
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

    public function getCategories(Request $request)
    {
        $categories = Category::arrangeUser()->paginate(10);
        $status = collect(['status' => 200]);
        $categories->appends($request->all())->render();
        $result = $status->merge($categories);
        return response($result);
    }

    public function getPartners(Request $request, $id)
    {
        $partners = Partner::arrangeUser()->with('campaign')->where('category_id', $id)->paginate(10);
        $status = collect(['status' => 200]);
        $partners->appends($request->all())->render();
        $result = $status->merge($partners);
        return response($result);
    }

    public function delete($id)
    {
        $category = Category::arrangeUser()->find($id);
        $category->small_icon != null ? $category->small_icon->remove() : $a = "";
        $category->large_icon != null ? $category->large_icon->remove() : $a = "";
        $category->forceDelete();
        $result = ['status' => 200];
        return response($result);
    }

    public function update(Request $request)
    {
        $category = Category::arrangeUser()->find($request->get('id'));
        if ($category)
        {
            if ($request->has('name_en'))
            {
                $category->name_en = $request->get('name_en');
            }

            if($request->has('name_az')){
              $category->name_az = $request->get('name_az');
            }

            if($request->has('name_ru')){
              $category->name_ru = $request->get('name_ru');
            }

            if ($request->hasFile('large_icon'))
            {
                $large_icon = new Photo();
                $mimeType = $request->file('large_icon');
                $large_icon_result = $large_icon->upload($request->file('large_icon'), 'uploads/photos/categories/');

                if ($large_icon_result == 200)
                {
                    $small_icon = new Photo();
                    $small_icon->url = $large_icon->resize($mimeType, 100, 100, 'uploads/photos/categories/');
                    $small_icon->save();
                    $category->small_icon != null ? $category->small_icon->remove() : $a = "";
                    $category->large_icon != null ? $category->large_icon->remove() : $a = "";
                    $category->large_icon_id = $large_icon->id;
                    $category->small_icon_id = $small_icon->id;
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