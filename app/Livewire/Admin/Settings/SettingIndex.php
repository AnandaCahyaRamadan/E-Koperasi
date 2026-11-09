<?php

namespace App\Livewire\Admin\Settings;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Setting;

class SettingIndex extends Component
{
    use WithFileUploads;

    public $koperasi_name;
    public $koperasi_address;
    public $koperasi_phone;
    public $koperasi_email;
    public $qris_merchant_name;
    public $qris_nmid;
    public $qris_image;
    public $existing_qris_image;

    public function mount()
    {
        $this->koperasi_name = Setting::get('koperasi_name', 'E-Koperasi Karyawan Sejahtera');
        $this->koperasi_address = Setting::get('koperasi_address', 'Gedung Kantor Pusat Lt. 1');
        $this->koperasi_phone = Setting::get('koperasi_phone', '081234567890');
        $this->koperasi_email = Setting::get('koperasi_email', 'koperasi@kantor.go.id');
        $this->qris_merchant_name = Setting::get('qris_merchant_name', 'KOPERASI PEGAWAI KANTOR');
        $this->qris_nmid = Setting::get('qris_nmid', 'ID1020030040506');
        $this->existing_qris_image = Setting::get('qris_image', null);
    }

    public function save()
    {
        $this->validate([
            'koperasi_name' => ['required', 'string', 'max:255'],
            'koperasi_address' => ['required', 'string', 'max:500'],
            'koperasi_phone' => ['required', 'string', 'max:50'],
            'koperasi_email' => ['required', 'email', 'max:100'],
            'qris_merchant_name' => ['required', 'string', 'max:100'],
            'qris_nmid' => ['required', 'string', 'max:100'],
            'qris_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        Setting::set('koperasi_name', $this->koperasi_name, 'general', 'Nama Koperasi');
        Setting::set('koperasi_address', $this->koperasi_address, 'general', 'Alamat Koperasi');
        Setting::set('koperasi_phone', $this->koperasi_phone, 'general', 'Nomor Kontak');
        Setting::set('koperasi_email', $this->koperasi_email, 'general', 'Email Koperasi');
        Setting::set('qris_merchant_name', $this->qris_merchant_name, 'payment', 'Nama Merchant QRIS');
        Setting::set('qris_nmid', $this->qris_nmid, 'payment', 'NMID QRIS');

        if ($this->qris_image) {
            $path = $this->qris_image->store('qris', 'public');
            Setting::set('qris_image', $path, 'payment', 'Gambar QRIS');
            $this->existing_qris_image = $path;
            $this->reset('qris_image');
        }

        session()->flash('success', 'Pengaturan sistem dan QRIS koperasi berhasil disimpan.');
    }

    public function render()
    {
        return view('livewire.admin.settings.setting-index')->layout('layouts.admin');
    }
}

