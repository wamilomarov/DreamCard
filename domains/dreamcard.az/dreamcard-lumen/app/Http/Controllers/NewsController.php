<?php
/**
 * Created by PhpStorm.
 * User: wamil
 * Date: 23-Oct-17
 * Time: 01:13
 */

namespace App\Http\Controllers;


use App\News;
use App\Photo;
use Illuminate\Http\Request;

class NewsController extends Controller
{

    public function create(Request $request)
    {
        if ($request->has('title') && $request->has('content') &&
            $request->hasFile('photo') && $request->has('partner_id'))
        {
            $news = new News();

            $photo = new Photo();

            $photo_upload_result = $photo->upload($request->file('photo'),   'uploads/photos/news/');

            if ($photo_upload_result == 200)
            {
                $news->photo_id = $photo->id;
                $news->title = $request->get('title');
                $news->content = $request->get('content');
                $news->partner_id = $request->get('partner_id');
                $news->save();

                $result = ['status' => 200, 'data' => $news];
            }
            else
            {
                $result = ['status' => $photo_upload_result];
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
        $news = News::arrangeUser()->find($id);
        $result = ['status' => 200, 'data' => $news];
        return response($result);

    }

    public function delete($id)
    {
        $news = News::arrangeUser()->find($id);
        $news->deletePhoto();
        $news->forceDelete();

        return response(['status' => 200]);
    }

    public function getNews()
    {
        $news = News::arrangeUser()->latest()->paginate(10);
        $status = collect(['status' => 200]);
        $result = $status->merge($news);
        return response($result);
    }

    public function update(Request $request)
    {
        $news = News::arrangeUser()->find($request->get('id'));

        if ($news)
        {
            if ($request->has('title'))
            {
                $news->title = $request->get('title');
            }

            if ($request->has('content'))
            {
                $news->content = $request->get('content');
            }

            if ($request->has('partner_id'))
            {
                $news->partner_id = $request->get('partner_id');
            }

            if ($request->hasFile('photo'))
            {
                $photo = new Photo();
                $photo_result = $photo->upload($request->photo('photo'), 'uploads/photos/news/');

                if ($photo_result == 200)
                {
                    $news->deletePhoto();
                    $news->photo_id = $photo->id;
                }
                else
                {
                    return response(['status' => $photo_result]);
                }
            }

            $news->save();
            $result = ['status' => 200];
        }
        else
        {
            $result = ['status' => 408];
        }

        return response($result);

    }


}