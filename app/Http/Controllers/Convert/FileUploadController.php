<?php

namespace App\Http\Controllers\Convert;

use App\Http\Requests\ConversionRequest;
use App\Http\Controllers\Controller;
//use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
//use Illuminate\Support\Facades\Storage;
use Illuminate\Http\RedirectResponse;

use App\Models\Conversion;
use App\Models\UserFile;
use App\Models\UserSetting;

// Not used?  (Replaced by Livewire component ConvertFile.)
class FileUploadController extends Controller
{
    public function upload(ConversionRequest $request): RedirectResponse
    {
        $validatedRequest = $request->validated();

        $file = $request->file('file');

        // write file to user_files table
        $sourceFile = new UserFile;
        $sourceFile->user_id = Auth::id();
        $sourceFile->file_type = $file->getClientMimeType();
        $sourceFile->file_size = $file->getSize();
        $sourceFile->original_filename = $file->getClientOriginalName();
        $sourceFile->type = 'SRC';
        $sourceFile->save();

        $file->storeAs(
            'files',
            Auth::id() . '-' . $sourceFile->id . '-source.txt',
            'public',
        );

        unset($validatedRequest['file']);

        if ($request->save_settings) {
            $userSetting = UserSetting::firstOrNew(
                ['user_id' => Auth::id()]
            );
            $userSetting->fill($validatedRequest);
            $userSetting->save();
        }

        $validatedRequest['user_file_id'] = $sourceFile->id;

        $conversion = new Conversion;
        $conversion->fill($validatedRequest);
        $conversion->user_id = Auth::id();
        $conversion->save();

        return redirect('convert/' . $conversion->id);
    }
}