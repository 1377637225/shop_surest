<?php

namespace App\Http\Controllers;

use App\Exceptions\WishException;
use App\Models\Wish;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishController extends Controller
{
    public function list()
    {
        $products = Wish::getProducts();

        return view('wish.list', compact('products') );
    }

    // 删除收藏
    public function delete(Request $request)
    {
        $pid = $request->id;
        if ($pid) {
            if( Wish::remove($pid) ){
                return response()->json([
                    'message' => '删除成功'
                ], 200);
            }
        }

        return response()->json([
            'message' => '未找到'
        ], 404);

    }

    public function add(Request $request)
    {
        try {
            $pid = $request->id;
            $user = \Auth::user();

            if ( $pid && $user ) {
                $wish = Wish::where('user_id', $user->id)->select('product_ids','id')->first();

                if( $wish ){
                    $ids = $wish->product_ids;

                    if (in_array($pid,$ids)) {
                        return response()->json([
                            'message' => '已经收藏了亲'
                        ], 404);
                    }

                    array_push($ids, $pid);

                } else {
                    $wish = new Wish();
                    $ids = [$pid];
                }
                $wish->user_id = $user->id;
                $wish->product_ids = $ids;
                $wish->save();

                # 给予收藏的创分数*2
                event(new \App\Events\ActiveUser($wish->user_id,2));

                return response()->json([
                    'message' => '喜欢成功'
                ], 200);
            }

            return response()->json([
                'message' => '非法操作'
            ], 404);

        }catch (\Exception $e) {
            throw new WishException([
                'message' => $user->name . '-' . $pid .  '-' .$e->getMessage()
            ]);
        };
    }
}
