<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductSize;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ProductController extends Controller
{
    public function index()
    {
        $product = Product::orderBy('created_at', 'DESC')->with(['product_images', 'sizes'])->get();
        return response()->json([
            'status' => 200,
            'data' => $product
        ], 200);
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'price' => 'required|numeric',
            'category_id' => 'required|integer',
            'sku' => 'required|unique:products,sku',
            'status' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors()
            ], 400);
        }

        $product = Product::create([
            'title'             => $request->title,
            'price'             => $request->price,
            'compare_price'     => $request->compare_price,
            'description'       => $request->description,
            'short_description' => $request->short_description,
            // 'image'             => $request->image,
            'category_id'       => $request->category_id,
            'brands_id'         => $request->brands_id,
            'qty'               => $request->qty,
            'sku'               => $request->sku,
            'barcode'           => $request->barcode,
            'status'            => $request->status,
            'is_featured'       => $request->is_featured,
        ]);


        if (!empty($request->sizes)) {
            foreach ($request->sizes as  $sizesId) {
                $productSize =  new ProductSize();
                $productSize->size_id = $sizesId;
                $productSize->product_id =  $product->id;
                $productSize->save();
            }
        }

        if (!empty($request->gallery)) {

            foreach ($request->gallery as $key => $tempImageId) {

                $tempImage = TempImage::find($tempImageId);

                if (!$tempImage) {
                    continue;
                }

                // Large Image
                $extArray = explode('.', $tempImage->name);
                $ext = end($extArray);

                $imageName = $product->id . '-' . time() . '.' . $ext;

                $manager = new ImageManager(new Driver());

                $img = $manager->read(public_path('uploads/temp/' . $tempImage->name));

                $img->scaleDown(1200);

                $img->save(public_path('uploads/products/large/' . $imageName));

                // Small Image
                $img = $manager->read(public_path('uploads/temp/' . $tempImage->name));

                $img->coverDown(400, 460);

                $img->save(public_path('uploads/products/small/' . $imageName));

                // Save Product Image
                $productImage = new ProductImage();

                $productImage->image = $imageName;

                $productImage->product_id = $product->id;

                $productImage->save();

                // Main Product Image
                if ($key == 0) {

                    $product->image = $imageName;

                    $product->save();
                }
            }
        }

        return response()->json([
            'status' => 200,
            'message' => 'Product added successfully',
            'product' => $product
        ]);
    }


    public function show(int $id)
    {
        $product = Product::with(['product_images', 'sizes'])->find($id);

        if (!$product) {
            return response()->json([
                'status' => 404,
                'message' => 'Product not found'
            ], 404);
        }

        $productSizes = $product->sizes()->pluck('size_id');

        return response()->json([
            'status' => 200,
            'data' => $product,
            'sizes' => $productSizes
        ]);
    }

    public function update(Request $request, int $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'status' => 404,
                'message' => 'Product not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'title'             => 'sometimes|string',
            'price'             => 'sometimes|numeric',
            'compare_price'     => 'sometimes|numeric|nullable',
            'description'       => 'sometimes|nullable|string',
            'short_description' => 'sometimes|nullable|string',
            'image'             => 'sometimes|nullable|string',
            'category_id'       => 'sometimes|integer',
            'brands_id'         => 'sometimes|integer|nullable',
            'qty'               => 'sometimes|integer|nullable',
            'sku'               => 'sometimes|string|unique:products,sku,' . $id,
            'barcode'           => 'sometimes|string',
            'status'            => 'sometimes|integer',
            'is_featured'       => 'sometimes|in:yes,no',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors()
            ], 400);
        }



        $product->update([
            'title' => $request->title ?? $product->title,

            'price' => $request->price ?? $product->price,

            'compare_price' => $request->compare_price ?? $product->compare_price,

            'description' => $request->description ?? $product->description,

            'short_description' => $request->short_description ?? $product->short_description,

            'image' => $request->image ?? $product->image,

            'category_id' => $request->category_id ?? $product->category_id,

            'brands_id' => $request->brands_id ?? $product->brands_id,

            'qty' => $request->qty ?? $product->qty,

            'sku' => $request->sku ?? $product->sku,

            'barcode' => $request->barcode ?? $product->barcode,

            'status' => $request->status ?? $product->status,

            'is_featured' => $request->is_featured ?? $product->is_featured,
        ]);

        if (!empty($request->sizes)) {
            ProductSize::where('product_id', $product->id)->delete();
            foreach ($request->sizes as  $sizesId) {
                $productSize =  new ProductSize();
                $productSize->size_id = $sizesId;
                $productSize->product_id =  $product->id;
                $productSize->save();
            }
        }

        return response()->json([
            'status' => 200,
            'message' => 'Product updated successfully',
            'product' => $product
        ]);
    }

    public function destroy(int $id)
    {
        $product = Product::with('product_images')->find($id);

        if (!$product) {
            return response()->json([
                'status' => 404,
                'message' => 'Product not found'
            ], 404);
        }

        $product->delete();

        if ($product->product_images) {
            foreach ($product->product_images as $productImage) {
                File::delete(public_path('uploads/products/large/' . $productImage->image));
                File::delete(public_path('uploads/products/small/' . $productImage->image));
            }
        }

        return response()->json([
            'status' => 200,
            'message' => 'Product deleted successfully'
        ]);
    }

    public function saveProductImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,jpg,png',
            'product_id' => 'required|exists:products,id'
        ]);

        if ($validator->fails()) {

            return response()->json([
                'status' => 400,
                'errors' => $validator->errors()
            ], 400);
        }

        $image = $request->file('image');

        $imageName = $request->product_id . '-' . time() . '.' . $image->extension();

        // Large Thumbnail
        $manager = new ImageManager(new Driver());

        $img = $manager->read($image->getPathname());

        $img->scaleDown(1200);

        $img->save(public_path('uploads/products/large/' . $imageName));

        // Small Thumbnail
        $img = $manager->read($image->getPathname());

        $img->coverDown(400, 460);

        $img->save(public_path('uploads/products/small/' . $imageName));

        // Save image in DB
        $productImage = new ProductImage();

        $productImage->image = $imageName;

        $productImage->product_id = $request->product_id;

        $productImage->save();

        return response()->json([
            'status' => 200,
            'message' => 'Image uploaded successfully',
            'data' => $productImage
        ], 200);
    }

    public function updateDefaultImage(Request $request)
    {
        $product = Product::find($request->product_id);
        $product->image = $request->image;
        $product->save();

        return response()->json([
            'status' => 200,
            'message' => ' Product Default Image changed successfully'
        ], 200);
    }

    public function deleteProductImage(int $id)
    {
        $productImage = ProductImage::find($id);
        if ($productImage == null) {
            return response()->json([
                'status' => 400,
                'message' => 'Image Not Found'
            ], 404);
        }
        File::delete(public_path('uploads/products/large/' . $productImage->image));
        File::delete(public_path('uploads/products/small/' . $productImage->image));

        $productImage->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Product Image deleted successfullyy..'
        ], 200);
    }
}
