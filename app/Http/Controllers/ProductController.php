<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\ProductColor;
use App\Models\ProductGallery;
use App\Models\ProductSize;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    const PATH_VIEW = 'admin.products.';
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Product::query()->with(['category'])->get();
//        dd($data->first()->category);
        return view(self::PATH_VIEW.__FUNCTION__, compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::query()->pluck('name', 'id')->all();
        $sizes = ProductSize::query()->pluck('name', 'id')->all();
        $colors = ProductColor::query()->pluck('name', 'id')->all();
        return view(self::PATH_VIEW.__FUNCTION__, compact('categories', 'sizes', 'colors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
//        dd($request->all());
        $data = $request->except(['product_variants', 'img_thumb', 'product_galleries']);
        $data['is_best_sale'] = isset($data['is_best_sale']) ? 1: 0;
        $data['is_40_sale'] = isset($data['is_40_sale']) ? 1: 0;
        $data['is_hot_online'] = isset($data['is_hot_online']) ? 1: 0;
        $data['slug'] = Str::slug($data['name'].'-'.$data['sku']);
        if (!empty($request->hasFile('img_thumb'))) {
            $data['img_thumb'] = Storage::put('products', $request->file('img_thumb'));
        }
        // xử lý dữ liệu variant
        $listProVariants = $request->product_variants;
        $dataProVariants = [];
        foreach ($listProVariants as $item) {
            $dataProVariants[] = [
                'product_size_id' => $item['size'],
                'product_color_id' => $item['color'],
                'image' => !empty($item['image']) ? Storage::put('product_variants', $item['image']) : '',
                'quantity' => !empty($item['quantity']) ? !empty($item['quantity']) : 0,
                'price' => !empty($item['price']) ? !empty($item['price']) : 0,
            ];
        }

        // xử lý dữ liệu product_galleries
        $listProGalleries = $request->product_galleries ?: [];
        $dataProGalleries = [];
        foreach ($listProGalleries as $image) {
            if(!empty($image)) {
                $dataProGalleries[] = [
                    'image' => Storage::put('product_galleries', $image)
                ];
            }
        }

        try {
            DB::beginTransaction();
            // tạo dữ liệu bảng product
            $product = Product::query()->create($data);
            // tạo dữ liệu cho bảng product variants
            foreach ($dataProVariants as $item) {
                $item += ['product_id'=> $product->id];
                ProductVariant::query()->create($item);
            }
            // tạo dữ liệu cho bảng product gallery
            foreach ($dataProGalleries as $item) {
                $item += ['product_id' => $product->id];
                ProductGallery::query()->create($item);
            }
            DB::commit();
            return redirect()->route('admin.products.index');
        } catch (\Exception $exception) {
            DB::rollBack();
            dd($exception->getMessage());
            // thực hiện xóa ảnh trong storage
            return back();
        }


    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return view(self::PATH_VIEW.__FUNCTION__, compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        return view(self::PATH_VIEW.__FUNCTION__, compact('product'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        try {
            DB::beginTransaction();
            $product->galleries()->delete();
            // Xóa order
            $product->variants()->delete();
            $product->delete();
            // Xóa ảnh trong storage
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return back();
        }
    }
}
