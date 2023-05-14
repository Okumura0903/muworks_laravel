<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\News;
use Illuminate\Support\Facades\Log;

class NewsController extends Controller
{
    public function index(){
        $news=News::all()->sortByDesc('sort_order');
        return view('admin.dashboard',compact('news'));
    }

    public function create(){
        return view('admin.create');
    }

    public function store(Request $request){
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required'],
        ]);
        $maxOrder=News::max('sort_order') ?? 1;
        News::create([
            'title' => $request->title,
            'content' => $request->content,
            'sort_order' => $maxOrder+1,
        ]);
        return redirect()->route('admin.dashboard');
    }

    public function show($id){
        $news=News::findOrFail($id);
        return view('admin.show',compact('news'));
    }

    public function edit($id){
        $news=News::findOrFail($id);
        return view('admin.edit',compact('news'));
    }

    public function update(Request $request){
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required'],
        ]);
        $news=News::find($request->id);
        $news->title=$request->title;
        $news->content=$request->content;
        $news->save();
        return redirect()->route('admin.dashboard');
    }
    public function delete($id){
        $news=News::findOrFail($id);
        $sort_order=$news->sort_order;
        $news->delete();
    
        News::where('sort_order', '>', $sort_order)->increment('sort_order',-1);;

        return redirect()->route('admin.dashboard');
    }

    //ドラッグアンドドロップで並び替え
    public function sort(Request $request){
        $target_id = str_replace('item_', '', $request->target);
        $target=News::findOrFail($target_id);

        $place_id=str_replace('item_', '', $request->place);
        $place=News::findOrFail($place_id);

        //大⇒小の場合
        if($target->sort_order>$place->sort_order){
            $target_new_order = $request->side==1 ? $place->sort_order+1 : $place->sort_order;
            News::where('sort_order', '<', $target->sort_order)->
                where('sort_order', '>=', $target_new_order)->
                increment('sort_order',1);
            $target->sort_order=$target_new_order;
            $target->save();
        }
        //小⇒大の場合
        elseif($target->sort_order<$place->sort_order){
            $target_new_order = $request->side==1 ? $place->sort_order : $place->sort_order-1;
                News::where('sort_order', '>', $target->sort_order)->
                    where('sort_order', '<=', $target_new_order)->
                    increment('sort_order',-1);
            $target->sort_order=$target_new_order;
            $target->save();
        }
//        Log::debug("target_order".$target->sort_order."target_id=".$target_id."place_order=".$place->sort_order."place_id=".$place_id);
    }
}
