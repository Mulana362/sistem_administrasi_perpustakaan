<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | TAMPILKAN SEMUA ANGGOTA
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        // kalau mau paginate tinggal ganti ->paginate(10)
        $members = Member::orderBy('name')->get();

        return view('members.index', compact('members'));
    }

    /*
    |--------------------------------------------------------------------------
    | FORM TAMBAH ANGGOTA
    |--------------------------------------------------------------------------
    */
    public function create()
    {
        return view('members.create');
    }

    /*
    |--------------------------------------------------------------------------
    | SIMPAN ANGGOTA BARU
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $request->validate([
            'nis'   => 'required|unique:members,nis',
            'name'  => 'required|string|max:255',
            'class' => 'required|string|max:50',
        ]);

        Member::create([
            'nis'   => $request->nis,
            'name'  => $request->name,
            'class' => $request->class,
        ]);

        return redirect()
            ->route('members.index')
            ->with('success', 'Anggota berhasil ditambahkan.');
    }

    /*
    |--------------------------------------------------------------------------
    | FORM EDIT ANGGOTA
    |--------------------------------------------------------------------------
    */
    public function edit(Member $member)
    {
        return view('members.edit', compact('member'));
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE ANGGOTA
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, Member $member)
    {
        $request->validate([
            'nis'   => 'required|unique:members,nis,' . $member->id,
            'name'  => 'required|string|max:255',
            'class' => 'required|string|max:50',
        ]);

        $member->update([
            'nis'   => $request->nis,
            'name'  => $request->name,
            'class' => $request->class,
        ]);

        return redirect()
            ->route('members.index')
            ->with('success', 'Anggota berhasil diperbarui.');
    }

    /*
    |--------------------------------------------------------------------------
    | HAPUS ANGGOTA
    |--------------------------------------------------------------------------
    */
    public function destroy(Member $member)
    {
        $member->delete();

        return redirect()
            ->route('members.index')
            ->with('success', 'Anggota berhasil dihapus.');
    }

    /*
    |--------------------------------------------------------------------------
    | CETAK KARTU ANGGOTA (PRINT FRIENDLY)
    |--------------------------------------------------------------------------
    */
    public function cetakKartu($id)
    {
        $member = Member::findOrFail($id);

        // View file: resources/views/members/cetak-kartu.blade.php
        return view('members.cetak-kartu', compact('member'));
    }
}
