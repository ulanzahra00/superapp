<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    public function index()
    {
        $schoolId = request()->user()->school_id;

        return view('news.index', [
            'news' => News::with('author')->where('school_id', $schoolId)->latest('published_at')->paginate(9),
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
            'image_url' => ['nullable', 'url', 'max:255'],
            'image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,gif', 'max:102400'],
        ]);

        unset($data['image']);

        if ($request->hasFile('image')) {
            $directory = public_path('uploads/news');
            if (! is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            $file = $request->file('image');
            $filename = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)).'-'.Str::random(8).'.'.$file->getClientOriginalExtension();
            $file->move($directory, $filename);
            $data['image_url'] = asset('uploads/news/'.$filename);
        }

        $data['author_id'] = $request->user()->id;
        $data['school_id'] = $request->user()->school_id;
        $data['slug'] = Str::slug($data['title']).'-'.Str::random(5);
        $data['published_at'] = now();

        News::create($data);

        return redirect()->route('news.index')->with('status', 'Berita sekolah berhasil dipublikasikan.');
    }

    public function show(News $news)
    {
        abort_unless($news->school_id === request()->user()->school_id, 403);

        return view('news.show', compact('news'));
    }
}
