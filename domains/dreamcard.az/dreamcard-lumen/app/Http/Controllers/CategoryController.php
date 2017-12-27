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
    private $categoryInstance;
    public function __construct()
    {
        $this->categoryInstance = new Category();
    }
    public function create(Request $request)
    {
        if ($request->has('name') && $request->hasFile('small_icon') && $request->hasFile('large_icon'))
        {
            if (!$this->categoryInstance->where('name', $request->get('name'))->exists())
            {
                $small_icon = new Photo();
                $small_icon_result = $small_icon->upload($request->file('small_icon'), 'uploads/photos/categories/');

                $large_icon = new Photo();
                $large_icon_result = $large_icon->upload($request->file('large_icon'), 'uploads/photos/categories/');

                if ($small_icon_result == 200)
                {
                    if ($large_icon_result == 200)
                    {
                        $category = $this->categoryInstance;
                        $category->name = $request->get('name');
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

    public function get($id)
    {
        $category = $this->categoryInstance->find($id);
        $result = ['status' => 200, 'data' => $category];
        return response($result);
    }

    public function getCategories()
    {
        $categories = $this->categoryInstance->paginate(10);
        $status = collect(['status' => 200]);
        $result = $status->merge($categories);
        return response($result);
    }

    public function delete($id)
    {
        $category = $this->categoryInstance->find($id);
        $category->deleteLargeIcon();
        $category->deleteSmallIcon();
        $category->forceDelete();
        $result = ['status' => 200];
        return response($result);
    }

    public function update(Request $request)
    {
        $request = $request->json();
        $category = $this->categoryInstance->find($request->get('id'));
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
                    $category->deleteLargeIcon();
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
        $category = $this->categoryInstance->find($id);
        $category->delete();
        $result = ['status' => 200];
        return response($result);
    }
}