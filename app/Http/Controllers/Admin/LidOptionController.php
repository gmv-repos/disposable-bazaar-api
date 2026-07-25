<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\LidOption;

class LidOptionController extends Controller
{
    public function index()
    {
        $lidOptions = LidOption::all();
        return view('adminPanel.lid_options.index', compact('lidOptions'));
    }
    public function create()
    {
        $html = view('adminPanel.lid_options.create')->render();

        return response()->json(
            [
                'success' => true,
                'message' => 'Add New Lid Option.',
                'html' => $html,
            ],
            200,
        );
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'image' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Validation Errors.',
                    'errors' => $validator->errors(),
                ],
                422,
            );
        }

        $imagePath = null;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = uniqid() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('lid_images', $filename);
            $imagePath = '/storage/' . $imagePath;
        }

        LidOption::create([
            'name' => $request->name,
            'image' => $imagePath,
            'img_alt' => $request->img_alt,
            'img_name' => $request->img_name,
        ]);

        return response()->json(
            [
                'success' => true,
                'message' => 'Lid option successfully created.',
            ],
            200,
        );
    }

    public function edit($id)
    {
        $lidOption = LidOption::findOrFail($id);

        $html = view('adminPanel.lid_options.edit', compact('lidOption'))->render();

        return response()->json(
            [
                'success' => true,
                'message' => 'Edit Lid Option.',
                'html' => $html,
            ],
            200,
        );
    }
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Validation Errors.',
                    'errors' => $validator->errors(),
                ],
                422,
            );
        }

        $lidOption = LidOption::find($id);

        if (!$lidOption) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Lid Option not found.',
                ],
                404,
            );
        }

        if ($request->hasFile('image')) {
            $image = $request->file('image');

            $filename = uniqid() . '.' . $image->getClientOriginalExtension();

            $prevImagePath = str_replace('/storage/', '', $lidOption->image);

            if (Storage::exists($prevImagePath)) {
                Storage::delete($prevImagePath);
            }

            $imagePath = $image->storeAs('lid_images', $filename);
            $lidOption->image = '/storage/' . $imagePath;
        }

        $lidOption->name = $request->name;
        $lidOption->img_alt = $request->img_alt;
        $lidOption->img_name = $request->img_name;

        $lidOption->save();

        return response()->json(
            [
                'success' => true,
                'message' => 'Lid Option updated successfully!',
                'data' => $lidOption,
            ],
            200,
        );
    }

    public function destroy($id)
    {
        $lidOption = LidOption::findOrFail($id);
        $lidOption->delete();
        return redirect()->route('product.lids.index')->with('success', 'LidOption deleted successfully!');
    }
}
