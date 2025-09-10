<?php

namespace App\Http\Controllers\Pengguna;

use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\StringHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Services\AuditTrailService;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Profile\ProfileModel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use App\Models\Pengaturan\AplikasiModel;
use App\DataTables\AdministratorDataTable;
use App\Http\Requests\Pengguna\AdministratorRequest;
use Illuminate\Contracts\Encryption\DecryptException;

class AdministratorController extends Controller
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

    public function index(AdministratorDataTable $dataTable)
    {
        $breadCumb = $this->breadCumb(['url' => route('administrator.index'), 'active' => '', 'aria' => '']);
        $breadCumb[] =  ['title' => 'Administrator', 'url' => 'javascript:void(0);', 'active' => 'active', 'aria' => 'aria-current="page"'];

        $data = [
            'title' => 'Administrator - ' . config('app.name'),
            'pageTitle' => 'Administrator',
            'breadCumb' => $breadCumb,
            'infoApp' => $this->infoApp
        ];

        return $dataTable->render('admin.pengguna.administrator.data-administrator', $data);
    }

    public function form(Request $request)
    {
        $param = $request->param;
        $id = $request->id ? Crypt::decrypt($request->id) : null;
        $user = $id ? User::with('profile')->find($id) : null;

        if ($param == 'add') {
            $title = 'Tambah Administrator';
        } else {
            if (!$user) {
                return redirect()->route('administrator.index')->with('error', 'Data Administrator tidak ditemukan.');
            }
            $title = 'Edit Administrator';
        }

        $breadCumb = $this->breadCumb(['url' => 'javascript:void(0);', 'active' => '', 'aria' => '']);
        $breadCumb[] =  ['title' => 'Administrator', 'url' => route('administrator.index'), 'active' => '', 'aria' => ''];
        $breadCumb[] =  ['title' => $title, 'url' => 'javascript:void(0);', 'active' => 'active', 'aria' => 'aria-current="page"'];

        $data = [
            'title' => $title . ' - ' . config('app.name'),
            'pageTitle' => $title,
            'breadCumb' => $breadCumb,
            'infoApp' => $this->infoApp,
            'user' => $user,
        ];

        return view('admin.pengguna.administrator.form-administrator', $data);
    }

    public function store(AdministratorRequest $request): JsonResponse
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
                $user = User::find($id);
                if (!$user) {
                    return response()->json(['success' => false, 'message' => 'Data administrator tidak ditemukan.'], 404);
                }

                // Update profile
                $profile = $user->profile ?? new ProfileModel();
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
                    $profileData['foto'] = $validated['foto']->store($fotoPath, 'public');
                }
                $profile->update($profileData);

                // Update user
                $userData = [
                    'name' => $validated['nama'],
                    'email' => $validated['email'],
                    'role' => $validated['role'],
                    'block' => $validated['aktif'], // 'aktif' field now directly maps to 'block'
                ];

                if (!empty($validated['password'])) {
                    $userData['password'] = Hash::make($validated['password']);
                }

                $user->update($userData);

                AuditTrailService::record('memperbarui data administrator : ' . $validated['nama'] . ' pada ' . now()->format('d F Y, h:i A'));
                $message = 'Data administrator berhasil diubah.';
            } else {
                // Create new record
                $splitName = StringHelper::splitName($validated['nama']);
                $profileData = [
                    'nama_depan' => $splitName['first_name'],
                    'nama_belakang' => $splitName['last_name'],
                    'kontak' => $validated['kontak'],
                ];

                if (isset($validated['foto'])) {
                    $profileData['foto'] = $validated['foto']->store($fotoPath, 'public');
                }
                $profile = ProfileModel::create($profileData);

                User::create([
                    'name' => $validated['nama'],
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                    'role' => $validated['role'],
                    'block' => $validated['aktif'], // 'aktif' field now directly maps to 'block'
                    'reactivation' => '0',
                    'profile_id' => $profile->id,
                    'profile_status' => '0'
                ]);

                AuditTrailService::record('menambahkan data administrator : ' . $validated['nama'] . ' pada ' . now()->format('d F Y, h:i A'));
                $message = 'Data administrator berhasil ditambahkan.';
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => $message]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving administrator: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
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
                Log::warning('Gagal mendekripsi ID administrator untuk dihapus: ' . $id, ['error' => $e->getMessage()]);
                return response()->json(['success' => false, 'message' => 'ID data tidak valid.'], 400);
            }

            $user = User::with('profile')->find($decryptedId);

            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Data administrator tidak ditemukan.'], 404);
            }

            // Safely delete profile and its photo if it exists
            if ($user->profile) {
                if ($user->profile->foto && Storage::disk('public')->exists($user->profile->foto)) {
                    Storage::disk('public')->delete($user->profile->foto);
                }
                $user->profile->delete();
            }
            AuditTrailService::record('menghapus data administrator : ' . $user->name . ' pada ' . now()->format('d F Y, h:i A'));

            $user->delete();
            DB::commit();

            return response()->json(['success' => true, 'message' => 'Data administrator berhasil dihapus.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting administrator: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan pada server.'], 500);
        }
    }

    public function detail(Request $request)
    {
        $id = $request->id ? Crypt::decrypt($request->id) : null;
        $user = $id ? User::with('profile')->find($id) : null;

        $breadCumb = $this->breadCumb(['url' => route('administrator.index'), 'active' => '', 'aria' => '']);
        $breadCumb[] =  ['title' => 'Administrator', 'url' => 'javascript:void(0);', 'active' => 'active', 'aria' => 'aria-current="page"'];
        $breadCumb[] =  ['title' => 'Detail', 'url' => 'javascript:void(0);', 'active' => 'active', 'aria' => 'aria-current="page"'];

        $data = [
            'title' => 'Detail - ' . config('app.name'),
            'pageTitle' => 'Detail Administrator',
            'breadCumb' => $breadCumb,
            'infoApp' => $this->infoApp,
            'user' => $user,
            'detailTitle' => $user->name,
        ];

        return view('admin.pengguna.administrator.detail-administrator', $data);
    }
}
