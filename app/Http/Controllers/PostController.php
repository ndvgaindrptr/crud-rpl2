<?php

namespace App\Http\Controllers;
use App\Models\Post;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index()
    {
    //get post5
    $posts = Post::latest()->paginate(5);

    // render view with posts
    return view('posts.index', compact('posts'));
    //
}
//langkah berikutnya
public function create() 
{
    return view('posts.create');
}
/**
 * store
 * 
 * @param Request $request
 * @return void
 */
public function store(Request $request): RedirectResponse
{
    //validate form
    $this->validate($request, [
        'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'title'  => 'required|min:5',
        'content' => 'required|min:10'
    ]);
    //upload image
    $image = $request->file('image');
    $image->storeAs('public/posts', $image->hashName());
    //create post
    Post::create([
        'image'   => $image->hashName(),
        'title'   => $request->title,
        'content' => $request->content
    ]);
    return redirect()->route('posts.index')->with(['success' => 'Data Berhasil Disimpan']);
    
}
public function edit(Post $post): View
{
    return view('posts.edit', compact('post'));
}
public function update(Request $request, Post $post): RedirectResponse
{
    //validate form
    $this->validate($request, [
        'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'title'  => 'required|min:5',
        'content' => 'required|min:10'
    ]);
    //check if image is uploaded
    if ($request->hasFile('image')) {
        //upload new image
        $image = $request->file('image');
        $image->storeAs('public/posts', $image->hashName());
        //delete old image
        Storage::delete('public/posts/'.$post->image);
        //update post with new image
        $post->update([
            'image'   => $image->hashName(),
            'title'   => $request->title,
            'content' => $request->content
        ]);
    }else{

        //update post without image
        $post->update([
            'title'  => $request->title,
            'content'=> $request->content
        ]);
    }

    //redirect to index 
    return redirect()->route('posts.index')->with(['success' => 'Data Berhasil Di Ubah']);

}

   public function destroy(Post $post)
   {
    //delete image
    Storage::delete('public/posts/'. $post->image);
    //delete post
    $post->delete();
    //redirect to index
    return redirect()->route('posts.index')->with(['success' => 'Data Berhasil Di Hapus']);
   }
   public function show(string $id):View
   {
    //get post by ID
    $post = Post::findDrFail($id);
    //render view with post
    return view('posts.show', compact('post'));
   }
}