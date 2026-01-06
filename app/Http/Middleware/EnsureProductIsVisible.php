<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Product;

class EnsureProductIsVisible
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $productId = $request->productId;
        $product = Product::find($productId);
        if($product !== null) {
            if($product->visible === 1) {
                return $next($request);
            } else {
                return redirect()->route('warning')->with('Warning', '物品已下架');
            }
        } else {
            return redirect()->route('warning')->with('Warning', '沒有此物品');
        }
    }
}
