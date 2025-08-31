<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Room;
use App\Models\User;
use App\Models\Booking;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'admin'
        ]);

        // Create normal user
        User::create([
            'name' => 'User Test',
            'email' => 'user@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'user'
        ]);

        // Create sample rooms
        $rooms = [
            [
                'name' => 'Phòng Thu Vocal A',
                'description' => 'Phòng thu âm chuyên nghiệp với cách âm 2 lớp, phù hợp cho thu âm vocal, podcast',
                'hourly_rate' => 200000,
                'status' => 'active',
                'image_url' => '/images/rooms/room1.jpg'
            ],
            [
                'name' => 'Phòng Thu Nhạc Cụ B',
                'description' => 'Phòng thu rộng rãi, thích hợp cho ban nhạc, có sẵn các nhạc cụ cơ bản',
                'hourly_rate' => 350000,
                'status' => 'active',
                'image_url' => '/images/rooms/room2.jpg'
            ],
            [
                'name' => 'Phòng Thu Mini C',
                'description' => 'Phòng thu nhỏ gọn, giá rẻ, phù hợp cho thu demo, tập hát',
                'hourly_rate' => 150000,
                'status' => 'active',
                'image_url' => '/images/rooms/room3.jpg'
            ],
            [
                'name' => 'Phòng Thu Premium D',
                'description' => 'Phòng thu cao cấp với thiết bị hiện đại nhất, phù hợp cho các dự án chuyên nghiệp',
                'hourly_rate' => 500000,
                'status' => 'active',
                'image_url' => '/images/rooms/room4.jpg'
            ],
            [
                'name' => 'Phòng Thu Band E',
                'description' => 'Phòng thu lớn dành cho ban nhạc, có phòng live riêng biệt',
                'hourly_rate' => 400000,
                'status' => 'active',
                'image_url' => '/images/rooms/room5.jpg'
            ],
            [
                'name' => 'Phòng Thu Podcast F',
                'description' => 'Thiết kế đặc biệt cho podcast, livestream với thiết bị chuyên dụng',
                'hourly_rate' => 250000,
                'status' => 'active',
                'image_url' => '/images/rooms/room6.jpg'
            ],
        ];

        foreach ($rooms as $room) {
            Room::create($room);
        }
    }
}