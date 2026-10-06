<?php

namespace App\Http\Controllers;

use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class TempImageController extends Controller
{

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,jpg,png'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'error' => $validator->errors()
            ], 400);
        }

        $tempImage = new TempImage();
        $tempImage->name = "Dummy Image";
        $tempImage->save();

        $image = $request->file('image');
        $imageName = time() . '.' . $image->extension();
        $image->move(public_path('uploads/temp'), $imageName);

        $tempImage->name = $imageName;
        $tempImage->save();


        //save image thumbnail
        $manager = new ImageManager(new Driver());

        $img = $manager->read(public_path('uploads/temp/' . $imageName));

        $img->coverDown(400, 450);

        $img->save(public_path('uploads/temp/thumb/' . $imageName));

        return response()->json([
            'status' => 200,
            'message' => 'Image has been uploaded successfully...!',
            'data' => $tempImage
        ], 200);
    }
}
