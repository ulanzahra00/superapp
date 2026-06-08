<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    public function index()
    {
        return view('news.index', [
            'news' => News::with('author')->latest('published_at')->paginate(9),
        ]);
    }

    public function create()
    {
        return view('news.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'excerpt' => ['required', 'string', 'max:500'],
            'content' => ['required', 'string'],
            'cover_color' => ['required', 'string', 'max:30'],
        ]);

        $data['author_id'] = $request->user()->id;
        $data['slug'] = Str::slug($data['title']).'-'.Str::random(5);
        $data['published_at'] = now();

        News::create($data);

        return redirect()->route('news.index')->with('status', 'Berita sekolah berhasil dipublikasikan.');
    }

    public function show(News $news)
    {
        return view('news.show', compact('news'));
    }
}
