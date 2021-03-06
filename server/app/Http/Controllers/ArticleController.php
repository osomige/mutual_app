<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Article;
use App\Http\Requests\ArticleRequest;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::all()->sortByDesc('created_at');
        // ここで日付ごとにソートしておく
        return view('articles.index', ['articles' => $articles]);
    }
    public function create()
    {
        return view('articles.create');    
    }
    public function store(ArticleRequest $request, Article $article)
    {
        if ($file = $request->img_path) {
            $fileName =$file->getClientOriginalName();
            $target_path = public_path('storage/diet_img/');
            $filename = $request->img_path->storeAs('public/diet_img', $fileName);
        } else {
            $fileName = "";
        }
        
        // dd($article->img_path);
        $article->img_path = 'storage/diet_img/' . $fileName;

        $article->title = $request->title;
        $article->body = $request->body;
        $article->user_id = $request->user()->id;
        $article->save();
        return redirect()->route('articles.index');
    }
    public function edit(Article $article)
    {
        return view('articles.edit', ['article' => $article]);    
    }
    public function show(Article $article)
    {
        return view('articles.show', ['article' => $article]);
    }    
    public function like(Request $request, Article $article)
    {
        $article->likes()->detach($request->user()->id);
        $article->likes()->attach($request->user()->id);

        return [
            'id' => $article->id,
            'countLikes' => $article->count_likes,
        ];
    }

    public function unlike(Request $request, Article $article)
    {
        $article->likes()->detach($request->user()->id);

        return [
            'id' => $article->id,
            'countLikes' => $article->count_likes,
        ];
    }
    public function update(ArticleRequest $request, Article $article)
    {
        $article->fill($request->all())->save();
        return redirect()->route('articles.index');
    }
    public function destroy(Article $article)
    {
        $article->delete();
        return redirect()->route('articles.index');
    }
}
