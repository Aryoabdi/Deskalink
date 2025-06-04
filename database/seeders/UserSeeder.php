<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Design;
use App\Models\DesignPreview;
use App\Models\Service;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Create admin users
        $adminUmar = User::create([
            'user_id' => 'user00000001',
            'username' => 'AdminUmar',
            'password' => Hash::make('password123'),
            'full_name' => 'Umar Mukhtar',
            'email' => 'adminumar@mail.com',
            'phone_number' => '083146978084',
            'role' => 'admin',
            'status' => 'active',
            'is_profile_completed' => true,
            'bio' => 'Admin'
        ]);

        $adminAryo = User::create([
            'user_id' => 'user00000003',
            'username' => 'AdminAryo',
            'password' => Hash::make('password123'),
            'full_name' => 'MOKHAMMAD AFRYLIANTO ARYO ABDI',
            'email' => 'adminaryo@mail.com',
            'phone_number' => '082333333333',
            'role' => 'admin',
            'status' => 'active',
            'is_profile_completed' => true,
            'bio' => 'Admin'
        ]);

        // Create partner user
        $partnerDimas = User::create([
            'user_id' => 'user00000002',
            'username' => 'PartnerDimas',
            'password' => Hash::make('password123'),
            'full_name' => 'Dimas Rhoyhan Budi Satrio',
            'email' => 'partnerdimas@mail.com',
            'phone_number' => '083123456789',
            'role' => 'partner',
            'status' => 'active',
            'is_profile_completed' => true,
            'bio' => 'Partner'
        ]);

        // Create designs for partner
        $design1 = Design::create([
            'design_id' => 'dsg0000001',
            'partner_id' => $partnerDimas->user_id, // Referencing partner's user_id
            'title' => 'Desain Interior Rumah Modern',
            'description' => 'Desain interior rumah modern minimalis siap pakai',
            'price' => 2000000,
            'status' => 'approved',
            'file_url' => 'https://drive.google.com/file/d/1RFNto0_6dvplkjLtqmpuQf4v--Cg6Ro8/view?usp=drive_link',
            'thumbnail' => 'https://i.postimg.cc/FRmcM4Sj/modern-minimalist-home-interio.jpg',
            'category' => 'Desain Interior'
        ]);

        // Create design previews for design1
        DesignPreview::create([
            'design_id' => $design1->design_id, // Referencing design1's design_id
            'image_url' => 'https://i.postimg.cc/9FfPXCnb/modern-minimalist-home-interio-1.jpg'
        ]);

        DesignPreview::create([
            'design_id' => $design1->design_id, // Referencing design1's design_id
            'image_url' => 'https://i.postimg.cc/FHM0xcsC/modern-minimalist-home-interio-2.jpg'
        ]);

        // Create services for partner
        Service::create([
            'service_id' => 'srv0000002',
            'partner_id' => $partnerDimas->user_id, // Referencing partner's user_id
            'title' => 'Gambar Teknik AutoCAD Rumah & Gedung',
            'description' => 'Gambar kerja lengkap arsitektur dan struktur bangunan.',
            'price' => 950000,
            'status' => 'approved',
            'category' => 'Teknik',
            'thumbnail' => 'https://i.postimg.cc/c48KKZvW/technical-drawing-3324368-1280.jpg'
        ]);

        Service::create([
            'service_id' => 'srv0000003',
            'partner_id' => $partnerDimas->user_id, // Referencing partner's user_id
            'title' => 'Interior Kamar Mandi Elegan',
            'description' => 'Layout furniture, pemilihan warna, dan pencahayaan ruangan.',
            'price' => 750000,
            'status' => 'pending',
            'category' => 'Interior',
            'thumbnail' => 'https://i.postimg.cc/hvYp2gng/3-D-cartoon-Disney-character-po-1.jpg'
        ]);
    }
}
