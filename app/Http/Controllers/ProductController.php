<?php

namespace App\Http\Controllers;
use App\Product;
use App\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class ProductController extends Controller
{
    public function home(){
        $products = Product::all();
        // dd($products);
        return view('product', ['products'=>$products]);
    }

    /**
     * product detail page
     */

    public function productDetail($id){
        $productDetails = Product::where('id', $id)->get();
        // dd($productDetails);
        return view('product-detail', ['productDetails'=>$productDetails]);
    }

    /**
     * Searching the products
     */

    public function search(Request $request){
        if($request->serch != null || $request->search != ""){
            $searchProduct = Product::where('name', 'like', '%'.$request->serch.'%')->get();
            if(sizeof($searchProduct) == 0){
                $searchProduct = "";
            }
        }
        else{
            $searchProduct = "";    
        }

        return view('search', ['searchProduct'=>$searchProduct]);
    }

    /**
     * Add To cart
     */

    public function addToCart(Request $request){

        // dd(session('user')['id']);
        if(session()->has('user')){
            $cart = new Cart;
            $cart->product_id = $request->product_id;
            $cart->	user_id = session('user')['id'];
            $cart->save();
            return redirect('/');;

        }else{
            redirect('/login');
        }
        
    }

    /**
     * Count the cart products
     */

    public static function cartCount(){
        $user_id= session('user')['id'];
        return Cart::where('user_id', $user_id)->count(); 
    }

    /**
     * Cart product listing
     */

    public function cartList(){
        $user_id= session('user')['id'];
        $data = DB::table('cart')
        ->join('products', 'cart.product_id', 'products.id')
        ->select('products.*', 'cart.id as cart_id')
        ->where('cart.user_id', $user_id)->paginate(5)->fragment('products');  
        // dd($data); 
        return view('cart-list', ['cartProduct'=>$data]);
    }

    /**
     * Remove from cart
     */

    public function removeProduct($id){
        Cart::destroy($id);
        return redirect('/cart-list')->with('message', 'Item is deleted from cart');
    } 


}
