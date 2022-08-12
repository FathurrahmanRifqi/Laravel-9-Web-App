<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index(){
        // get 
        $posts = Post::latest()->paginate(5);

        // render
        return view('posts.index',compact('posts'));
    }

    public function create(){
        return view('posts.create');
    }

    public function store(Request $request){
        // validate form 
        $this->validate($request,[
            'image'     => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'title'     => 'required|min:5',
            'content'   => 'required|min:10'
        ]);

        // upload image
        $image = $request->file('image');
        $image->storeAs('public/posts',$image->hashName());

        //create post
        Post::create([
            'image'     => $image->hashName(),
            'title'     => $request->title,
            'content'   => $request->content
        ]);

        // redirect to index 
        return redirect()->route('posts.index')->with(['success' => 'Data berhasil di simpan !']);
    }

    public function edit(Post $post){
        return view('posts.edit',compact('post'));
    }

    public function update(Request $request, Post $post){
        // validate form 
        $this->validate($request,[
            'title'     => 'required|min:5',
            'content'   => 'required|min:10'
        ]);

        // check if image is uploaded
        if($request->hasFile('image')){
            // validate form just image 
            $this->validate($request,[
                'image'     => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
            
            // upload new image
            $image = $request->file('image');
            $image->storeAs('public/posts',$image->hashName());

            // delete old image
            Storage::delete('public/posts/'.$post->image);
            
            // update post with new image
            $post->update([
                'image'     => $image->hashName(),
                'title'     => $request->title,
                'content'   => $request->content
            ]);
        }else{
            // upload post without image
            $post->update([
                'title'     => $request->title,
                'content'   => $request->content
            ]);
        }

        // redirect to index 
        return redirect()->route('posts.index')->with(['success' => 'Data berhasil di ubah !']);
    }

    public function destroy(Post $post){
        // delete image
        Storage::delete('public/posts/'.$post->image);

        // delete post
        $post->delete();

        // redirect to index 
        return redirect()->route('posts.index')->with(['success' => 'Data berhasil di hapus !']);


    }
}
