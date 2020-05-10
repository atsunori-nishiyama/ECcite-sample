<?php

namespace App\Http\Controllers;

use App\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Mail\Buy;
use Illuminate\Support\Facades\Mail;

class BuyController extends Controller
{
    //
    public function index()
    {
        $cartitems = CartItem::select('cart_items.*', 'items.name', 'items.amount')
            ->where('user_id', Auth::id())
            ->join('items', 'items.id', '=', 'cart_items.item_id')
            ->get();
        $subtotal = 0;
        foreach($cartitems as $cartitem){
            $subtotal += $cartitem->amount * $cartitem->quantity;
        }
        return view('buy/index', ['cartitems' => $cartitems, 'subtotal' => $subtotal]);
    }

    public function store(Request $request) //$requestによりフォームからの入力を受け取る
    {
        //フォームからのリクエストパラメータにpostという値が含まれているかどうかを判定
        if( $request->has('post') ){
            //postが含まれている場合は注文を確定する処理を実行

            //store()メソッドの購入完了ページを表示する前に、メール送信の処理を組み込む
            //ログイン中のユーザーのメールアドレスを取得し、Mail::to()メソッドに渡す事で送信先を設定
            //new Buy()でBuyクラスのインスタンスを生成し、Mail::send()メソッドに渡してメールを送信
            Mail::to(Auth::user()->email)->send(new Buy());

            //カート情報を削除し、同じ注文を何度も行ってしまわないようにする
            CartItem::where('user_id', Auth::id())->delete();
            //削除したら購入完了へ進む
            return view('buy/complete');
        }
        $request->flash(); //フォームのリクエスト情報をセッションに記録
        return $this->index(); //購入画面のビューを再度表示
    }
}
