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
        if ($request->has('name_az') && $request->has('name_en') && $request->has('name_ru') /*&& $request->hasFile('small_icon')*/ && $request->hasFile('dark_icon') && $request->hasFile('light_icon'))
        {
            if (!Category::arrangeUser()->where('name_az', $request->get('name_az'))->where('name_ru', $request->get('name_ru'))->where('name_en', $request->get('name_en'))->exists())
            {
                $darkMimeType = $request->file('dark_icon')->getMimeType();
                $small_dark_icon = new Photo();

                $large_dark_icon = new Photo();
                $large_dark_icon_result = $large_dark_icon->upload($request->file('dark_icon'), 'uploads/photos/categories/');

                $lightMimeType = $request->file('light_icon')->getMimeType();
                $small_light_icon = new Photo();

                $large_light_icon = new Photo();
                $large_light_icon_result = $large_light_icon->upload($request->file('light_icon'), 'uploads/photos/categories/');


                if ($large_light_icon_result == 200)
                {
                    if ($large_dark_icon_result == 200)
                    {
                        $small_dark_icon->url = $large_dark_icon->resize($darkMimeType, 100, 100, 'uploads/photos/categories/');
                        $small_dark_icon->save();

                        $small_light_icon->url = $large_light_icon->resize($lightMimeType, 100, 100, 'uploads/photos/categories/');
                        $small_light_icon->save();

                        $category = new Category();
                        $category->name_az = $request->get('name_az');
                        $category->name_ru = $request->get('name_ru');
                        $category->name_en = $request->get('name_en');
                        $category->small_dark_icon_id = $small_dark_icon->id;
                        $category->large_dark_icon_id = $large_dark_icon->id;
                        $category->small_light_icon_id = $small_light_icon->id;
                        $category->large_light_icon_id = $large_light_icon->id;

                        if ($request->has('order_by'))
                        {
                            $category->order_by = $request->get('order_by');
                        }

                        $category->save();
                        $result = ['status' => 200];
                    }
                    else
                    {
                        $result = ['status' => $large_dark_icon_result];
                    }
                }
                else
                {
                    $result = ['status' => $large_light_icon_result];
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
        $category->small_light_icon != null ? $category->small_light_icon->remove() : $a = "";
        $category->large_light_icon != null ? $category->large_light_icon->remove() : $a = "";
        $category->small_dark_icon != null ? $category->small_dark_icon->remove() : $a = "";
        $category->large_dark_icon != null ? $category->large_dark_icon->remove() : $a = "";
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

            if ($request->hasFile('dark_icon'))
            {
                $large_dark_icon = new Photo();
                $darkMimeType = $request->file('dark_icon')->getMimeType();
                $large_dark_icon_result = $large_dark_icon->upload($request->file('dark_icon'), 'uploads/photos/categories/');

                if ($large_dark_icon_result == 200)
                {
                    $small_dark_icon = new Photo();
                    $small_dark_icon->url = $large_dark_icon->resize($darkMimeType, 100, 100, 'uploads/photos/categories/');
                    $small_dark_icon->save();
                    $category->small_dark_icon != null ? $category->small_dark_icon->remove() : $a = "";
                    $category->large_dark_icon != null ? $category->large_dark_icon->remove() : $a = "";
                    $category->large_dark_icon_id = $large_dark_icon->id;
                    $category->small_dark_icon_id = $small_dark_icon->id;
                }
                else
                {
                    return response(['status' => $large_dark_icon_result]);
                }
            }

            if ($request->hasFile('light_icon'))
            {
                $large_light_icon = new Photo();
                $lightMimeType = $request->file('light_icon')->getMimeType();
                $large_light_icon_result = $large_light_icon->upload($request->file('light_icon'), 'uploads/photos/categories/');

                if ($large_light_icon_result == 200)
                {
                    $small_light_icon = new Photo();
                    $small_light_icon->url = $large_light_icon->resize($lightMimeType, 100, 100, 'uploads/photos/categories/');
                    $small_light_icon->save();
                    $category->small_light_icon != null ? $category->small_light_icon->remove() : $a = "";
                    $category->large_light_icon != null ? $category->large_light_icon->remove() : $a = "";
                    $category->large_light_icon_id = $large_light_icon->id;
                    $category->small_light_icon_id = $small_light_icon->id;
                }
                else
                {
                    return response(['status' => $large_light_icon_result]);
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