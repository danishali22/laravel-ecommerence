<?php

namespace App\Http\Controllers;

use App\Models\TempImage;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class TempImagesController extends Controller
{
    public function create(Request $request){
        $image = $request->image;
        if(!empty($image)){
            $ext = $image->getClientOriginalExtension();

            $newName = time(). '.' .$ext;

            $tempImage = new TempImage();
            $tempImage->name = $newName;
            $tempImage->save();

            $image->move(public_path().'/temp', $newName);

            // Generate Thumbnail
            $manager = new ImageManager(new Driver());
            $sourcePath = public_path().'/temp/'. $newName;
            $destPath = public_path().'/temp/thumb/'. $newName;
            $img = $manager->read($sourcePath);
            //$img = $img->resize(450, 600);
            $img = $img->cover(300, 275);
            $img->save($destPath);

            return response()->json([
                'success' => 'true',
                'image_id' => $tempImage->id,
                'image_path' => asset('/temp/thumb/'.$newName),
                'message' => 'Image Uploaded Successfully',
            ]);
        }
    }
}
