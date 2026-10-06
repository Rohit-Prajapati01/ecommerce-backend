<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Brands;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class BrandsController extends Controller
{
    public function index()
    {
        $brands = Brands::orderBy('created_at', 'DESC')->get();
        return response()->json([
            'status' => 200,
            'data' => $brands
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'data' => $validator->errors()
            ], 400);
        }

        $Brands = new Brands();
        $Brands->name = $request->name;
        $Brands->status = $request->status;
        $Brands->save();

        return response()->json([
            'status' => 200,
            'message' => 'Brands Added Successfully.',
            'data' => $Brands
        ], 200);
    }

    public function show( int $id)
    {
        $Brands = Brands::find($id);

        if ($Brands == null) {
            return response()->json([
                'status' => 404,
                'message' => 'Brands Not Found.',
                'data' => $Brands
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'data' => $Brands
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $brand = Brands::find($id);

        if (!$brand) {
            return response()->json([
                'status' => 404,
                'message' => 'Brand Not Found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'status' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422, // 👈 better than 400
                'errors' => $validator->errors()
            ], 422);
        }

        $brand->update([
            'name' => $request->name,
            'status' => $request->status ?? 1
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Brand Updated Successfully',
            'data' => $brand
        ], 200);
    }

    public function destroy( int $id)
    {
        $Brands = Brands::find($id);
        if ($Brands == null) {
            return response()->json([
                'status' => 404,
                'message' => 'Brands Not Found.',
                'data' => $Brands
            ], 404);
        } else {
            $Brands->delete();
            return response()->json([
                'status' => 200,
                'message' => 'Brands Deleted Successfully.',
            ], 200);
        }
    }
}
