<?php

namespace App\Http\Controllers;

use App\Events\NotifyEvent;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function show()
    {
        return view('front.post');
    }

    public function save(Request $request)
    {   
        $data = $request->validate([
            'title' => 'required|string',
            'author' => 'required|string',
            'body' => 'string',
        ]);

        $data['user_id'] = Auth::user()->id;

        Post::create($data);

        $event_data = [
            'title' => $data['title'],
            'author' => $data['author'],
            'body' => $data['body'],
            'user_id' => $data['user_id'],
        ];

        event(new NotifyEvent($event_data));

        return response()->json([
           'status' => true,
           'data' => $data,
           'event_data' => $event_data,
           'message' => 'Post saved successfully',
        ]);

        return redirect()->back();
    }
}
