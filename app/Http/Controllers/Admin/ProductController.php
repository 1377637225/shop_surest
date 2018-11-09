<?php
/**
 * Created by PhpStorm.
 * User: surest.cn
 * Date: 2018/11/8
 * Time: 19:55
 */

namespace App\Http\Controllers\Admin;

use App\Exceptions\UploadException;
use App\Handle\ImageUploadHandler;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\ProductCreateRequest;
use App\Http\Requests\Admin\ProductUpdateRequest;
use App\Services\ProductService;
use App\Models\Image;
use App\Exceptions\ModelException;

class ProductController
{
    public function list()
    {
        $products = Product::getProductsAll();
        $productsAll = Product::getProductsAll(false);

        return view('admin.product.list',compact('products','productsAll'));
    }

    // 添加用户
    public function addOrEdit(Request $request)
    {
        $product = null;
        $id = $request->id ?? null;
        if( $id ) {
            $product = Product::with(['image','category','productSkus.image'])->find($id);
        }
        $categoies = Category::all();
        return view('admin.product.add_edit',compact('product','id','categoies'));

    }

    /**
     * 图片上传
     * @param Request $request
     * @param ImageUploadHandler $upload
     * @return \Illuminate\Http\JsonResponse
     * @throws UploadException
     */
    public function upload(Request $request,ImageUploadHandler $upload)
    {
        $result = [
            'code' => 0,
            'msg' => '上传组件出现问题，请检查日志',
            'data' => [
                'src' => '',
                'title' => 'upload'
            ]
        ];

        $file = $request->file;

        if( !$file_path = $upload->upload($file,'product',500) ){
            throw new UploadException();
        }
        $result['data']['src'] = $file_path;
        return response()->json($result,200);
    }

    /**
     * 创建商品数据
     * @param ProductCreateRequest $request
     * @param ProductService $productService
     * @throws ModelException
     */
    public function create(ProductCreateRequest $request,ProductService $productService)
    {
        try{
            \DB::BeginTransaction();
            # 写入商品的数据
            $result = $productService->getParams($request);
            # 创建商品
            $product = Product::create($result);
            # 写入商品的图片数据
            $img = $request->product_img;
            $pid = $product->id;

            Image::create([
                'src' => $img,
                'product_id' => $pid
            ]);

            # 写入sku
            $skus = $productService->getSkusParms($request,$product->id);

            $id[0] = \DB::table('product_skus')->insertGetId($skus[1]);
            $id[1] = \DB::table('product_skus')->insertGetId($skus[2]);

            Image::create([
                'src' => $request->skus['new1']['skuImg'],
                'product_sku_id' => $id[0],
            ]);
            Image::create([
                'src' => $request->skus['new2']['skuImg'],
                'product_sku_id' => $id[1],
            ]);

            \DB::commit();
        }catch (\Exception $e){
            \DB::rollback();
            throw new ModelException([
                'message' => "写入商品数据异常" . $e->getMessage()
            ]);

        }
    }

    public function update(ProductUpdateRequest $request)
    {

        $id = $request->id;
        if( !$id || !Product::find($id) ) {
            session()->flash('status','商品不存在');
            return redirect()->back();
        }


        dd($request->all());

    }
}