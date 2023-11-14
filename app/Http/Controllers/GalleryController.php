<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\File;


class GalleryController extends Controller
{
    public function index()
    {
        $data = array(
            'id' => "posts",
            'menu' => 'Gallery',
            'galleries' => Post::where('picture', '!=', '')->whereNotNull('picture')->orderBy('created_at', 'desc')->paginate(30)
        );
        return view('gallery.index')->with($data);
    }

    public function create()
    {
        return view('gallery.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|max:255',
            'description' => 'required',
            'picture' => 'image|nullable|max:1999'
        ]);

        if ($request->hasFile('picture')) {
            $filenameWithExt = $request->file('picture')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('picture')->getClientOriginalExtension();
            $basename = uniqid() . time();
            $smallFilename = "small_{$basename}.{$extension}";
            $mediumFilename = "medium_{$basename}.{$extension}";
            $largeFilename = "large_{$basename}.{$extension}";
            $filenameSimpan = "{$basename}.{$extension}";
            $path = $request->file('picture')->storeAs('posts_image', $filenameSimpan);
        } else {
            $filenameSimpan = 'noimage.png';
        }

        $post = new Post;
        $post->picture = $filenameSimpan;
        $post->title = $request->input('title');
        $post->description = $request->input('description');
        $post->save();
        return redirect('gallery')->with('success', 'Berhasil menambahkan data baru');
    }

    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $movie = Post::findOrFail($id);
        return view('gallery.edit', compact('movie'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $movie = Post::findOrFail($id);
        $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
            'picture' => 'image|nullable|max:1999'
        ]);
        $userData = [
            'title' => $request->title,
            'description' => $request->description,
        ];
        if ($request->hasFile('picture')) {
            File::delete(public_path() . '/storage/posts_image/' . $movie->picture);

            $filenameWithExt = $request->file('picture')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('picture')->getClientOriginalExtension();
            $filenameSimpan = "{$filename}_" . time() . ".{$extension}";
            $path = $request->file('picture')->storeAs('posts_image', $filenameSimpan);

            $userData['picture'] = $path;
        } else {
        }

        $movie->update($userData);

        return redirect('gallery')->with('success', 'Data successfully modified.');
    }

    public function destroy(string $id)
    {
        $user = Post::findOrFail($id);
        $user->delete();
        return redirect()->route('gallery.index')
            ->withSuccess('Data Deleted Successfully');
    }
}
