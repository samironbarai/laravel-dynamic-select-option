<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::all();
        return view('post.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::pluck('name', 'id');
        return view('post.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $categoryIds = $this->_getCategoryIds($request->categories);

        DB::beginTransaction();

        try {
            $post = new Post();
            $post->title = $request->title;
            $post->save();

            // Insert all selected category's id to category_post table
            $post->categories()->sync($categoryIds);
            DB::commit();
            // all good

            return redirect()->route('posts.index');
        } catch (\Exception $e) {
            DB::rollBack();
            // something went wrong

            Log::info('error on creating post ' . $e->getMessage());
        }
    }

    protected function _getCategoryIds($data)
    {
        $ids = array();
        foreach ($data as $value) {
            if ((int)$value) { // if we get number, push number to $ids array
                array_push($ids, (int)$value);
            } else { // if get text, create new category
                try {
                    $category = new  Category();
                    $category->name = $value;
                    $category->save();
                    array_push($ids, $category->id);
                } catch (\Exception $e) {
                    Log::info('error on creating category ' . $e->getMessage());
                }
            }
        }
        return $ids;
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Post $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Post $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Post $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Post $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        //
    }
}
