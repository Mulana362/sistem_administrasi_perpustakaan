<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;

class StudentLoginController extends Controller
{
    public function showLoginForm()
    {
        // arahkan ke file tampilan login siswa yang baru
        return view('auth.student-login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'nis'   => 'required',
            'name'  => 'required',
            'class' => 'required',
        ]);

        // cek data siswa cocok dengan tabel members
        $member = Member::where('nis', $request->nis)
                        ->where('name', $request->name)
                        ->where('class', $request->class)
                        ->first();

        // jika ditemukan
        if ($member) {
            session([
                'student_id'    => $member->id,
                'student_name'  => $member->name,
                'student_class' => $member->class
            ]);

            return redirect()->route('student.dashboard');
        }

        // jika tidak ditemukan
        return back()->with('error', 'Data siswa tidak ditemukan.');
    }

    public function logout()
    {
        session()->forget([
            'student_id',
            'student_name',
            'student_class',
            'visitor_number'
        ]);

        return redirect()->route('student.login');
    }
}
