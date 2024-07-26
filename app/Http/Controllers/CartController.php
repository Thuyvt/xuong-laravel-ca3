<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Http\Request;

class CartController extends Controller
{
    //
    public function list()
    {
        return view('cart');
    }
    public function add(Request $request) {
//        dd($request->all());
        // lấy taạm thông tin 1 tài khoản
        $user = User::query()->first();
//        dd($user);
        $cart = Cart::query()->where('user_id', $user->id)->first();
        // nếu chuwa có giỏ hàng thì tạo mới giỏ hàng
        if (empty($cart)) {
            $cart = Cart::query()->create(['user_id'=>$user->id]);
        }
        $productVariant = ProductVariant::query()->where([
            'product_id' => $request->product_id,
            'product_size_id' => $request->product_size_id,
            'product_color_id' => $request->product_color_id
        ])->first();
        $data = [
            'product_variant_id' => $productVariant->id,
            'cart_id' => $cart->id,
            'quantity' => $request->quantity
        ];
        // kiểm tra nếu trong giỏ hàng đã có product_variant_id thì cộng dồn số lượng
        $cartItem = CartItem::query()->where('product_variant_id', $productVariant->id)->first();
        if (empty($cartItem)) {
            CartItem::query()->create($data);
        } else {
            $data['quantity'] += $cartItem->quantity;
            $cartItem->update(['quantity' => $data['quantity']]);
        }
        return redirect()->route('cart.list');
    }
}
