<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use App\Services\ImageService;

class UserController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {   
        try {
            $user = User::find($id);
            
            if ($user) {
                return response()->json(['user' => $user], 200);
            } else {
                return response()->json(['user' => []], 200);
            }
        } catch (\Exeption $e) {
            return response()->json([
                'message' => 'Something went wrong in UserController.show',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, int $id)
    {
        try {
            $user = User::findOrFail($id);

            if ($request->hasFile('image')) {
                (new ImageService)->updateImage($user, $request, '/images/users/', 'update');
            }

            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->location = $request->location;
            $user->description = $request->description;

            $user->save();

            return response()->json('User details updated', 200);
        } catch (\Exeption $e) {
            return response()->json([
                'message' => 'Something went wrong in UserController.update',
                'error' => $e->getMessage(),
            ], 400);
        }
    }
}
