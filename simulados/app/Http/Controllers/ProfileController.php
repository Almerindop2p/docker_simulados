<?php

namespace App\Http\Controllers;

use App\Http\Requests\Profile\UpdateAvatarRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
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
        $oldPath = $user->avatar_path;

        $extension = strtolower($file->getClientOriginalExtension());
        $newPath = $file->storeAs('avatars', 'avatar_' . $user->id . '_' . Str::random(12) . '.' . $extension, 'local');

        if (!$newPath) {
            return redirect()
                ->route('perfil.show')
                ->withErrors(['avatar' => 'Nao foi possivel salvar o avatar. Tente novamente.']);
        }

        $user->avatar_path = $newPath;
        $user->save();

        $this->deleteAvatar($oldPath);
        $this->deleteUserAvatarGarbage((int) $user->id, $newPath);

        return redirect()
            ->route('perfil.show')
            ->with('status', 'Avatar atualizado com sucesso.');
    }

    private function deleteAvatar(?string $path): void
    {
        if (!$path) {
            return;
        }

        // Remove avatares antigos em public/ (legado).
        if (Str::startsWith($path, 'uploads/avatars/')) {
            $fullPath = public_path($path);

            if (File::exists($fullPath)) {
                File::delete($fullPath);
            }

            return;
        }

        if (Storage::disk('local')->exists($path)) {
            Storage::disk('local')->delete($path);
        }
    }

    private function deleteUserAvatarGarbage(int $userId, string $keepPath): void
    {
        $privatePrefix = 'avatars/avatar_' . $userId . '_';

        foreach (Storage::disk('local')->files('avatars') as $privatePath) {
            if (Str::startsWith($privatePath, $privatePrefix) && $privatePath !== $keepPath) {
                Storage::disk('local')->delete($privatePath);
            }
        }

        $legacyDir = public_path('uploads/avatars');
        $legacyPrefix = 'avatar_' . $userId . '_';

        if (!File::isDirectory($legacyDir)) {
            return;
        }

        foreach (File::files($legacyDir) as $legacyFile) {
            $legacyName = $legacyFile->getFilename();
            $legacyRelativePath = 'uploads/avatars/' . $legacyName;

            if (Str::startsWith($legacyName, $legacyPrefix) && $legacyRelativePath !== $keepPath) {
                File::delete($legacyFile->getPathname());
            }
        }
    }
}
