<?php

namespace App\Http\Controllers;

use App\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; //コントローラに認証情報（ユーザー情報）を扱う機能が追加

class CartItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //検索結果に含めるカラムを指定
        $cartitems = CartItem::select('cart_items.*', 'items.name', 'items.amount')
        ->where('user_id', Auth::id()) //ログイン中のユーザーのユーザーIDをキーにしてカート内の商品を検索
        ->join('items', 'items.id', '=', 'cart_items.item_id') //cart_itemsテーブルとitemsテーブルを結合
        ->get(); //検索結果を取得し、ビューに渡

        $subtotal = 0;
        foreach($cartitems as $cartitem){
            $subtotal += $cartitem->amount * $cartitem->quantity;
            //「単価×数量」の値を$subtotalに加算
        }

        return view('cartitem/index', ['cartitems' => $cartitems, 'subtotal' => $subtotal]);
        //$subtotalを追加し、小計をビューに渡

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        CartItem::updateOrCreate( //レコードの登録と更新を兼ねるメソッド

            //`user_idとitem_idが一致するレコードがすでに存在していた場合はquantity(数量)を加算し、
            //レコードが存在していなければ新規登録する
            //HTMLのフォーム等(POSTメソッド)で送られた値を取得
            [
                'user_id' => Auth::id(), //ユーザーIDはAuthの機能を使い、Auth::id()で取得
                'item_id' => $request->post('item_id'), //商品IDと数量は$request->post('キー名')を使って取得
            ],
            [
                'quantity' => \DB::raw('quantity +' .$request->post('quantity')),
            ]
            );
            return redirect('/')->with('flash_message', 'カートに追加しました');
            //with()に指定した引数の値をセッションデータに保存したうえでリダイレクト
            //セッションデータは一度リダイレクトしたら消える
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CartItem  $cartItem
     * @return \Illuminate\Http\Response
     */
    public function show(CartItem $cartItem)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CartItem  $cartItem
     * @return \Illuminate\Http\Response
     */
    public function edit(CartItem $cartItem)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CartItem  $cartItem
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CartItem $cartItem)
    {
        //更新する元の数量を上書き
        $cartItem->quantity = $request->post('quantity');
        $cartItem->save(); //データベースに保存
        return redirect('cartitem')->with('flash_message', 'カートを更新しました');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CartItem  $cartItem
     * @return \Illuminate\Http\Response
     */
    public function destroy(CartItem $cartItem)
    {
        //CartItemのモデルを取得し、delete()メソッドを呼び出せばそのレコードは削除
        $cartItem->delete();
        return redirect('cartitem')->with('flash_message', 'カートから削除しました');
    }
}
