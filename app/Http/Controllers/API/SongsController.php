<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Http\Requests\Song\StoreSongRequest;
use App\Models\Songs;
use App\Models\User;
use Illuminate\Http\Request;

class SongsController extends Controller
{

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSongRequest $request)
    {
        try {
            $file = $request->file;
            if (empty($file)) {
                return response()->json('No Song Uploaded', 400);
            }
    
            $user = User::findOrFail($request->get('user_id'));

            $song = $file->getClientOriginalName();
            $file->move('songs/' . $user->id, $song);

            Songs::create([
                'user_id' => $request->get('user_id'),
                'title' => $request->get('title'),
                'song' => $song,
            ]);

            return response()->json('Song Saved!', 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong in SongsController.store',
                'error' => $e->getMessage(),
            ], 400);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id, int $user_id)
    {
        try {
            $song = Songs::findOrFail($id);

            $currentSong = public_path() . "/songs/" . $user_id . "/" . $song->song;
            if (file_exists($currentSong)) {
                unlink($currentSong);
            }

            $song->delete();

            return response()->json('Song Deleted!', 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong in SongsController.destroy',
                'error' => $e->getMessage(),
            ], 400);
        }
    }
}
