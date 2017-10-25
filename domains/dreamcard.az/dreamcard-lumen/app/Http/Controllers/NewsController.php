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
            $request->hasFile('photo') && $request->has('category_id'))
        {
            $news = new News();

            $photo = new Photo();

            $photo_upload_result = $photo->upload($request->file('photo'),   'uploads/photos/news/');

            if ($photo_upload_result == 200)
            {
                $news->photo_id = $photo->id;
                $news->title = $request->get('title');
                $news->content = $request->get('content');
                $news->category_id = $request->get('category_id');
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
        $news = News::find($id);
        $result = ['status' => 200, 'data' => $news];
        return response($result);

    }

    public function delete($id)
    {
        $news = News::find($id);
        $news->deletePhoto();
        $news->delete();

        return response(['status' => 200]);
    }

    public function getNews()
    {
        $news = News::paginate(10);
        $status = collect(['status' => 200]);
        $result = $status->merge($news);
        return response($result);
    }

    public function update(Request $request)
    {
        $news = News::find($request->get('id'));

        if ($news)
        {

        }
        else
        {
            $result = ['status' => 408];
        }

    }

}