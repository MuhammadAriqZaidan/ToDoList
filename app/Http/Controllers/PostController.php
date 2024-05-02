<?php

namespace App\Http\Controllers;

use App\Models\post;
use App\Models\post as ModelsPost;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;


class PostController extends Controller
{
    public function index(): View
    {
     $posts = post::latest()->paginate(5);

     return View('post.index', compact('posts'));
    }



    public function create(): View
    {
        return view('post.create');
    }

    public function store(Request $request)
    {
        // Validasi Form
        $this->validate($request ,[
            'image'   => 'required|image|mimes:jpeg,jpg,png|max:2048',
            'title'   => 'required|min:5',
            'content' => 'required|min:10',
        ]);

        // Upload Image
        $image = $request->file('image');
        $image->storeAs('public/posts',  $image->hashName());

        // Create Data
        Post::create([
            'image'     => $image->hashName(),
            'title'     => $request->title,
            'content'   => $request->content
        ]);


        return redirect()->route('posts.index')->with(['success'=> 'Data Has Been Saved!']);
    }

    public function show(string $id): View
    {
        $post = Post::findOrFail($id);


        return view('post.show', compact('post'));
    }

    public function edit(string $id): View
    {
        $post = Post::findOrFail($id);

        return view('post.edit', compact('post'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $this->validate($request ,[
            'image'   => 'image|mimes:jpeg,jpg,png|max:2048',
            'title'   => 'required|min:5',
            'content' => 'required|min:10',
        ]);

        $post = Post::findOrFail($id);


        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $image->storeAs('public/posts/', $image->hashName());


            Storage::delete('public/posts/ .$post->image');

            $post->update([
                'image'     => $image->hashName(),
                'title'     => $request->title,
                'content'   => $request->content
            ]);


        } else {

            $post->update([
                'title'     => $request->title,
                'content'   => $request->content
            ]);
        }

        return redirect()->route('posts.index')->with(['success'=> 'Data Updated']);
    }

    public function destroy($id): RedirectResponse
    {
        $post = Post::findOrFail($id);

        Storage::delete('public/posts/'.$post->image);

        $post->delete();

        return redirect()->route('posts.index')->with(['success'=> 'Data Deleted']);
    }

}
