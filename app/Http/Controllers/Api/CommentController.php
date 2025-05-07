<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    // Tampilkan semua komentar berdasarkan konten dan user
    public function index($contentId)
    {
        $comments = Comment::where('id_content', $contentId)
            ->with('user:id,name') // Mengambil user dengan ID dan nama
            ->get();

        return response()->json($comments, 200);
    }

    // Tambahkan komentar
    public function store(Request $request)
    {
        $request->validate([
            'id_content' => 'required|exists:contents,id',
            'comment' => 'required|string|max:255',
        ]);

        $comment = Comment::create([
            'id_user' => Auth::id(),
            'id_content' => $request->id_content,
            'comment' => $request->comment,
            'date_added' => now(),
        ]);

        return response()->json(['message' => 'Comment added successfully', 'comment' => $comment], 201);
    }

    // Hapus komentar
    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);

        if ($comment->id_user !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully'], 200);
    }

    // Edit komentar
    public function update(Request $request, $id)
    {
        $comment = Comment::findOrFail($id);

        if ($comment->id_user !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'comment' => 'required|string|max:255',
        ]);

        $comment->update([
            'comment' => $request->comment,
        ]);

        return response()->json(['message' => 'Comment updated successfully', 'comment' => $comment], 200);
    }
}
