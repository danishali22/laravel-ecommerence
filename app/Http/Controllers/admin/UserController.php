<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::latest('id');
        if (!empty($request->get('keyword'))) {
            $users = $users->where('name', 'like', '%' . $request->get('keyword') . '%');
        }

        $users = $users->paginate(10);
        return view('admin.users.list', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|unique:users',
            'phone' => 'required',
            'password' => 'required|min:5',
            'status' => 'required',
        ]);

        if ($validator->passes()) {
            $users = new User();
            $users->name = $request->name;
            $users->email = $request->email;
            $users->phone = $request->phone;
            $users->password = Hash::make($request->password);
            $users->status = $request->status;
            $users->save();

            session()->flash('success', 'User Created Successfully');

            return response()->json([
                'status' => true,
                'message' => 'User Created Sucessfully',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'error' => $validator->errors(),
            ]);
        }
    }

    public function edit($id)
    {
        $users = User::find($id);
        if (empty($users)) {
            return redirect()->route('users.index');
        }
        return view('admin.users.edit', compact('users'));
    }

    public function update($id, Request $request)
    {
        $users = User::find($id);
        if (empty($users)) {
            session()->flash('error', 'User record not Found');
            return response()->json([
                'success' => false,
                'notFound' => true,
                'message' => 'User record not Found',
            ]);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|unique:users,email,' . $users->id . ',id',
            'phone' => 'required',
            'password' => 'required|min:5',
            'status' => 'required',
        ]);

        if ($validator->passes()) {
            $users->name = $request->name;
            $users->email = $request->email;
            $users->phone = $request->phone;

            if ($request->password != '') {
                $users->password = Hash::make($request->password);
            }

            $users->status = $request->status;
            $users->save();

            session()->flash('success', 'User Updated Successfully');

            return response()->json([
                'status' => true,
                'message' => 'User Updated Sucessfully',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'error' => $validator->errors(),
            ]);
        }
    }

    public function destroy($id)
    {
        $user = User::find($id);
        if (empty($user)) {
            session()->flash('error', 'User record not Found');

            return response()->json([
                'status' => false,
                'notFound' => true,
                'message' => 'User record not Found'
            ]);
        }
        $user->delete();

        session()->flash('success', 'User Deleted Successfully');

        return response()->json([
            'status' => true,
            'message' => 'User Deleted Successfully'
        ]);
    }
}
