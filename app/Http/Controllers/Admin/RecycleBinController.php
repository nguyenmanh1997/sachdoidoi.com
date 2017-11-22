<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Post;

class RecycleBinController extends Controller
{
	/**
	 * recycle bin posts
	 * @return [type] [description]
	 */
    public function posts(){
    	return view('admin.post.recycleBin');
    }

    public function undoPost(Request $request){
    	// dd($request->input('id'));
    	Post::where('id', $request->input('id'))->update([
        'is_trash' => 0,
        'status' => 1
      ]);

    	return redirect()->back();
    }

    public function deleteForeverPost(Request $request){
    	Post::where('id', $request->input('id'))->delete();
    	
    	return redirect()->back();
    }

    public function jsonListPost(){
    	$posts = Post::where('is_trash', 1)->get();

        return Datatables($posts)
            ->addColumn('action', function ($post) {
              return '
                <form action="'.route('admin.recycleBin.posts.undoPost').'" method="post" style="display: inline-block;">
                  <input type="hidden" name="_token" value="'.csrf_token().'">
                  <input type="hidden" name="_method" value="PUT">
                  <input type="hidden" name="id" value="'.$post->id.'">
                  <button type="submit" title="Click để khôi phục!" class="btn btn-xs btn-success">
                    <i class="fa fa-undo" aria-hidden="true"></i>
                  </button>
                </form>
                <form action="'.route('admin.recycleBin.posts.delete').'" method="post" style="display: inline-block;">
                  <input type="hidden" name="_token" value="'.csrf_token().'">
                  <input type="hidden" name="_method" value="DELETE">
                  <input type="hidden" name="id" value="'.$post->id.'">
                  <button type="button" class="btn btn-xs btn-danger delete-post" title="Click để xóa vĩnh viễn!">
                    <i class="glyphicon glyphicon-remove"></i>
                  </button>
                </form>
                ';
            })
            ->editColumn('featured_post', '{{ $featured_post == 1 ? "featured" : "" }}' )
            ->editColumn('status', '{{ $status == 1 ? "public" : "private" }}' )
            ->make(true);
    }
}