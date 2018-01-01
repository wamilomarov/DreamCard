<?php
/**
 * Created by PhpStorm.
 * User: wamil
 * Date: 28-Oct-17
 * Time: 03:50
 */

namespace App\Http\Controllers;


use App\Department;
use App\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DepartmentController extends Controller
{
    public function create(Request $request)
    {

        if ($request->has('name') && $request->has('category_id') &&  $request->hasFile('photo')
            && $request->has('username') && $request->has('password') && $request->has('city_id')
            && $request->has('partner_id'))
        {
            if (Department::arrangeUser()->where('name', $request->get('name'))->orWhere('username', $request->get('username'))->exists())
            {
                $result = ['status' => 407];
                return response()->json($result);
            }
            $department = new Department();

            $photo = new Photo();
            $photo_result = $photo->upload($request->file('photo'), 'uploads/photos/departments/');

            if ($photo_result == 200)
            {
                $department->name = $request->get('name');
                $department->photo_id = $photo->id;
                $department->category_id = $request->get('category_id');
                $department->city_id = $request->get('city_id');
                $department->partner_id = $request->get('partner_id');
                $department->username = $request->get('username');
                $department->password = app('hash')->make($request->get('password'));
                $department->save();
                $result = ['status' => 200];
            }
            else
            {
                $result = ['status' => $photo_result];
            }
        }
        else
        {
            $result = ['status' => 406];
        }
        return response($result);
    }

    public function update(Request $request)
    {
        $department = Department::arrangeUser()->find($request->get('id'));

        if ($department && $department->isEditableByGuard())
        {
            if ($request->has('name'))
            {
                $department->name = $request->get('name');

            }

            if ($request->has('category_id'))
            {
                $department->category_id = $request->get('category_id');

            }

            if ($request->has('city_id'))
            {
                $department->city_id = $request->get('city_id');

            }

            if ($request->has('partner_id'))
            {
                $department->partner_id = $request->get('partner_id');

            }

            if ($request->hasFile('photo'))
            {
                $photo = new Photo();
                $photo_result = $photo->upload($request->file('photo'), 'uploads/photos/departments/');

                if ($photo_result == 200)
                {
                    $department->deletePhoto();
                    $department->photo_id = $photo->id;
                }
                else
                {
                    return response(['status' => $photo_result]);
                }
            }

            if ($request->has('username'))
            {
                $department->username = $request->get('username');
            }

            if ($request->has('password') && $request->has('prev_password'))
            {
                if ($department && Hash::check($request->get('prev_password'), $department->getAuthPassword()))
                {
                    $department->password = app('hash')->make($request->get('password'));
                    $department->first_entry = 0;
                    $department->api_token = null; //  log out when password is changed
                }
                else
                {
                    return response(['status' => 409]);
                }
            }

            $department->save();
            $result = ['status' => 200];
        }
        else
        {
            $result = ['status' => 408];
        }

        return response($result);
    }

    public function getDepartments()
    {
        $departments = Department::arrangeUser()->paginate(10);
        $status = collect(['status' => 200]);
        $result = $status->merge($departments);
        return response($result);
    }

    public function get($id)
    {
        $department = Department::arrangeUser()->find($id);
        $result = ['status' => 200, 'data' => $department];
        return response($result);
    }

    public function delete($id)
    {
        $department = Department::arrangeUser()->find($id);
        $department->photo->remove();
        $department->forceDelete();
        $result = ['status' => 200];
        return response($result);
    }

    public function disable($id)
    {
        $department = Department::arrangeUser()->find($id);
        $department->delete();
        $result = ['status' => 200];
        return response($result);
    }

    public function restore($id)
    {
        $department = Department::arrangeUser()->find($id);
        $department->restore();
        $result = ['status' => 200];
        return response($result);
    }

}