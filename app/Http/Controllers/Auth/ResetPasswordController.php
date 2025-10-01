<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\SendResetLinkRequest;

class ResetPasswordController extends Controller
{
    protected $resetPasswordService;
    public function __construct()
    {
        $this->resetPasswordService = new \App\Services\ResetPasswordService();
    }

    public function index()
    {
        $data = [
            'title' => 'Reset Password - ' . config('app.name'),
        ];
        return view('auth.forgot-password', $data);
    }

    public function reset($token)
    {
        return $this->resetPasswordService->reset($token);
    }

    public function send(SendResetLinkRequest $request)
    {
        $validated = $request->validated();
        return $this->resetPasswordService->send($validated);
    }

    public function save(ResetPasswordRequest $request)
    {
        $validated = $request->validated();
        return $this->resetPasswordService->save($validated);
    }
}
