<?php

namespace App\Http\Controllers\Pengguna;

use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\StringHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Services\AuditTrailService;
use Illuminate\Support\Facades\Log;
use App\DataTables\AdvokatDataTable;
use App\Http\Controllers\Controller;
use App\Models\Profile\ProfileModel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use App\Models\Pengaturan\AplikasiModel;
use App\Http\Requests\Pengguna\AdvokatRequest;
use Illuminate\Contracts\Encryption\DecryptException;

class AdvokatNonAdvokatController extends Controller
{
    protected $infoApp;
    public function __construct()
    {
        $this->infoApp = Cache::memo()->remember('infoApp', 60, function () {
            return AplikasiModel::first();
        });
    }

    private function breadCumb($parameters)
    {
        $breadCumb = [
            ['title' => 'Pengguna', 'url' => $parameters['url'], 'active' => $parameters['active'], 'aria' => $parameters['aria']],
        ];

        return $breadCumb;
    }

    public function index(AdvokatDataTable $dataTable)
    {
        $breadCumb = $this->breadCumb(['url' => route('advokat.index'), 'active' => '', 'aria' => '']);
        $breadCumb[] =  ['title' => 'Advokat/Non Advokat', 'url' => 'javascript:void(0);', 'active' => 'active', 'aria' => 'aria-current="page"'];

        $data = [
            'title' => 'Adovkat/ Non Advokat - ' . config('app.name'),
            'pageTitle' => 'Adovkat/ Non Advokat',
            'breadCumb' => $breadCumb,
            'infoApp' => $this->infoApp
        ];

        return $dataTable->render('admin.pengguna.advokat-non-advokat.data-advokat-non-advokat', $data);
    }

    public function form(Request $request)
    {
        $param = $request->param;
        $id = $request->id ? Crypt::decrypt($request->id) : null;
        $user = $id ? User::with('profile')->find($id) : null;

        if ($param == 'add') {
            $title = 'Tambah Advokat/ Non Advokat';
        } else {
            if (!$user) {
                return redirect()->route('advokat.index')->with('error', 'Data Administrator tidak ditemukan.');
            }
            $title = 'Edit Advokat/ Non Advokat';
        }

        $breadCumb = $this->breadCumb(['url' => 'javascript:void(0);', 'active' => '', 'aria' => '']);
        $breadCumb[] =  ['title' => 'Advokat/ Non Advokat', 'url' => route('advokat.index'), 'active' => '', 'aria' => ''];
        $breadCumb[] =  ['title' => $title, 'url' => 'javascript:void(0);', 'active' => 'active', 'aria' => 'aria-current="page"'];

        $id = $request->id ? Crypt::decrypt($request->id) : null;

        $data = [
            'title' => $title . ' - ' . config('app.name'),
            'pageTitle' => $title,
            'breadCumb' => $breadCumb,
            'infoApp' => $this->infoApp,
            'user' => $user,
        ];

        return view('admin.pengguna.advokat-non-advokat.form-advokat-non-advokat', $data);
    }

    public function store(AdvokatRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $id = null;

        if ($request->filled('id')) {
            try {
                $id = Crypt::decrypt($request->input('id'));
            } catch (DecryptException $e) {
                return response()->json(['success' => false, 'message' => 'ID data tidak valid.'], 400);
            }
        }

        // Generate photo path user/profile/08/2025
        $fotoPath = 'user/profile/' . date('m') . '/' . date('Y');

        DB::beginTransaction();
        try {
            if ($id) {
                // Update existing record
                $user = User::with('profile')->find($id);
                if (!$user) {
                    return response()->json(['success' => false, 'message' => 'Data advokat/non advokat tidak ditemukan.'], 404);
                }

                // 1. Capture old data for audit trail
                $oldUserData = $user->only(['name', 'email', 'block']);
                $oldProfileData = $user->profile ? $user->profile->only(['kontak', 'foto']) : [];
                $oldData = array_merge($oldUserData, $oldProfileData);
                // Add a comparable 'aktif' field, which is the inverse of 'block'
                $oldData['aktif'] = !$user->block;

                // Update profile
                $profile = $user->profile ?? new ProfileModel();
                $newFotoPath = null;
                $splitName = StringHelper::splitName($validated['nama']);
                $profileData = [
                    'nama_depan' => $splitName['first_name'],
                    'nama_belakang' => $splitName['last_name'],
                    'kontak' => $validated['kontak'],
                ];

                if (isset($validated['foto'])) {
                    if ($profile->foto && Storage::disk('public')->exists($profile->foto)) {
                        Storage::disk('public')->delete($profile->foto);
                    }
                    $newFotoPath = $validated['foto']->store($fotoPath, 'public');
                    $profileData['foto'] = $newFotoPath;
                }
                $profile->update($profileData);

                // Update user
                $userData = [
                    'name' => $validated['nama'],
                    'email' => $validated['email'],
                    'block' => $validated['aktif'], // 'aktif' field now directly maps to 'block'
                ];

                if (!empty($validated['password'])) {
                    $userData['password'] = Hash::make($validated['password']);
                }

                $user->update($userData);

                // 2. Prepare new data for audit trail
                $newData = [
                    'nama' => $validated['nama'],
                    'email' => $validated['email'],
                    'kontak' => $validated['kontak'],
                    'aktif' => $validated['aktif'],
                ];
                if ($newFotoPath) {
                    $newData['foto'] = $newFotoPath;
                }

                // 3. Record detailed audit trail
                $context = ['old' => $oldData, 'new' => $newData];
                AuditTrailService::record('telah memperbarui data advokat/non advokat: ' . $validated['nama'], $context);
                $message = 'Data advokat/non advokat berhasil diubah.';
            } else {
                // Create new record
                $splitName = StringHelper::splitName($validated['nama']);
                $newFotoPath = null;
                $profileData = [
                    'nama_depan' => $splitName['first_name'],
                    'nama_belakang' => $splitName['last_name'],
                    'kontak' => $validated['kontak'],
                ];

                if (isset($validated['foto'])) {
                    $newFotoPath = $validated['foto']->store($fotoPath, 'public');
                    $profileData['foto'] = $newFotoPath;
                }
                $profile = ProfileModel::create($profileData);

                User::create([
                    'name' => $validated['nama'],
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                    'role' => 'User',
                    'block' => $validated['aktif'], // 'aktif' field now directly maps to 'block'
                    'reactivation' => '0',
                    'profile_id' => $profile->id,
                    'profile_status' => '0'
                ]);

                // Prepare new data for audit trail
                $newData = [
                    'nama' => $validated['nama'],
                    'email' => $validated['email'],
                    'kontak' => $validated['kontak'],
                    'aktif' => $validated['aktif'],
                ];
                if ($newFotoPath) {
                    $newData['foto'] = $newFotoPath;
                }

                // Record detailed audit trail
                $context = ['old' => [], 'new' => $newData];
                AuditTrailService::record('telah menambahkan data advokat/non advokat: ' . $validated['nama'], $context);
                $message = 'Data advokat/non advokat berhasil ditambahkan.';
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => $message]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving advokat/non advokat: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan pada server.'], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        DB::beginTransaction();
        try {
            try {
                $decryptedId = Crypt::decrypt($id);
            } catch (DecryptException $e) {
                Log::warning('Gagal mendekripsi ID advokat/non advokat untuk dihapus: ' . $id, ['error' => $e->getMessage()]);
                return response()->json(['success' => false, 'message' => 'ID data tidak valid.'], 400);
            }

            $user = User::with('profile')->find($decryptedId);

            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Data advokat/non advokat tidak ditemukan.'], 404);
            }

            // Capture data for audit trail before deletion
            $userName = $user->name;
            $oldData = $user->toArray(); // Captures the model and its loaded relations

            // Safely delete profile and its photo if it exists
            if ($user->profile) {
                if ($user->profile->foto && Storage::disk('public')->exists($user->profile->foto)) {
                    Storage::disk('public')->delete($user->profile->foto);
                }
                $user->profile->delete();
            }

            // Record the deletion in the audit trail
            $context = [
                'old' => $oldData,
                'new' => [], // 'new' is empty for a delete action
            ];
            AuditTrailService::record('telah menghapus data advokat/non advokat: ' . $userName, $context);

            $user->delete();
            DB::commit();

            return response()->json(['success' => true, 'message' => 'Data advokat/non advokat berhasil dihapus.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting advokat/non advokat: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan pada server.'], 500);
        }
    }

    public function detail(Request $request)
    {
        $id = $request->id ? Crypt::decrypt($request->id) : null;
        $user = $id ? User::with('profile')->find($id) : null;

        $breadCumb = $this->breadCumb(['url' => route('advokat.index'), 'active' => '', 'aria' => '']);
        $breadCumb[] =  ['title' => 'Advokat/Non Advokat', 'url' => 'javascript:void(0);', 'active' => 'active', 'aria' => 'aria-current="page"'];
        $breadCumb[] =  ['title' => 'Detail', 'url' => 'javascript:void(0);', 'active' => 'active', 'aria' => 'aria-current="page"'];

        $data = [
            'title' => 'Detail - ' . config('app.name'),
            'pageTitle' => 'Detail Advokat/Non Advokat',
            'breadCumb' => $breadCumb,
            'infoApp' => $this->infoApp,
            'user' => $user,
            'detailTitle' => $user->name,
        ];

        return view('admin.pengguna.advokat-non-advokat.detail-advokat-non-advokat', $data);
    }
}
