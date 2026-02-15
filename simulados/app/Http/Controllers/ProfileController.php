<?php

namespace App\Http\Controllers;

use App\Http\Requests\Profile\UpdateAvatarRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(Request $request): View
    {
        return view('perfil', [
            'user' => $request->user(),
        ]);
    }

    public function updateAvatar(UpdateAvatarRequest $request): RedirectResponse
    {
        $user = $request->user();
        $file = $request->file('avatar');

        $destination = public_path('uploads/avatars');
        if (!File::exists($destination)) {
            File::makeDirectory($destination, 0755, true);
        }

        $extension = strtolower($file->getClientOriginalExtension());
        $filename = 'avatar_' . $user->id . '_' . Str::random(12) . '.' . $extension;
        $file->move($destination, $filename);

        $newPath = 'uploads/avatars/' . $filename;
        $this->deleteAvatar($user->avatar_path);

        $user->avatar_path = $newPath;
        $user->save();

        return redirect()
            ->route('perfil.show')
            ->with('status', 'Avatar atualizado com sucesso.');
    }

    private function deleteAvatar(?string $path): void
    {
        if (!$path || !Str::startsWith($path, 'uploads/avatars/')) {
            return;
        }

        $fullPath = public_path($path);

        if (File::exists($fullPath)) {
            File::delete($fullPath);
        }
    }
}
