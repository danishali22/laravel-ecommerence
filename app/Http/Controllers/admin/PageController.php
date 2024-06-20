<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PageController extends Controller
{
    public function index(Request $request){
        $page = Page::latest();

        if($request->get('keyword') != ''){
            $page = $page->where('name', 'like', '%'. $request->get('keyword') .'%');
        }

        $page = $page->paginate(10);
        $data['pages'] = $page;

        return view('admin.pages.list', $data);
        
    }
    public function create(){
        return view('admin.pages.create');
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'error' => $validator->errors(),
            ]);
        }

        $page = new Page;
        $page->name = $request->name;
        $page->slug = $request->slug;
        $page->content = $request->content;
        $page->save();

        $message = 'Page Created Successfully';

        session()->flash('success', $message);

        return response()->json([
            'status' => true,
            'error' => $message,
        ]);
        
    }
    public function edit($id){
        $page = Page::find($id);

        if($page == null){
            return redirect()->route('pages.index');
        }

        return view('admin.pages.edit', [
            'page' => $page
        ]);
        
    }
    public function update(Request $request, $id){
        $page = Page::find($id);

        if($page == null){
            session()->flash('error', 'Page Record not Found');
            return response()->json([
                'status' => true
            ]);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'error' => $validator->errors(),
            ]);
        }

        $page->name = $request->name;
        $page->slug = $request->slug;
        $page->content = $request->content;
        $page->save();

        $message = 'Page Updated Successfully';

        session()->flash('success', $message);

        return response()->json([
            'status' => true,
            'error' => $message,
        ]);
        
    }
    public function destroy($id){
        $page = Page::find($id);

        if($page == null){
            session()->flash('error', 'Page Record not Found');
            return response()->json([
                'status' => true
            ]);
        }

        $page->delete();

        $message = 'Page Deleted Successfully';

        session()->flash('success', $message);

        return response()->json([
            'status' => true,
            'error' => $message,
        ]);
    }
}
