<?php

namespace Database\Seeders;

use App\Models\Space;
use App\Models\Event;
use Illuminate\Database\Seeder;

class SpaceBoothSeeder extends Seeder
{
    public function run(): void
    {
        // Get first event or use ID 1 as fallback
        $event = Event::first();
        $eventId = $event->id ?? 1;

        $spaces = [
            [
                'eventId' => $eventId,
                'name' => 'Booth A-101',
                'zone' => 'Hall A',
                'type' => 'booth',
                'size' => 'medium',
                'price' => 50000,
                'status' => 'occupied',
                'description' => 'Premium booth in Hall A with high foot traffic',
                'amenities' => json_encode(['electricity', 'wifi', 'water']),
            ],
            [
                'eventId' => $eventId,
                'name' => 'Booth A-102',
                'zone' => 'Hall A',
                'type' => 'booth',
                'size' => 'medium',
                'price' => 50000,
                'status' => 'occupied',
                'description' => 'Premium booth in Hall A',
                'amenities' => json_encode(['electricity', 'wifi']),
            ],
            [
                'eventId' => $eventId,
                'name' => 'Booth A-103',
                'zone' => 'Hall A',
                'type' => 'booth',
                'size' => 'small',
                'price' => 40000,
                'status' => 'occupied',
                'description' => 'Standard booth in Hall A',
                'amenities' => json_encode(['electricity', 'wifi']),
            ],
            [
                'eventId' => $eventId,
                'name' => 'Booth B-201',
                'zone' => 'Hall B',
                'type' => 'booth',
                'size' => 'large',
                'price' => 70000,
                'status' => 'occupied',
                'description' => 'Large booth in Hall B',
                'amenities' => json_encode(['electricity', 'wifi', 'water', 'ac']),
            ],
            [
                'eventId' => $eventId,
                'name' => 'Booth B-202',
                'zone' => 'Hall B',
                'type' => 'booth',
                'size' => 'large',
                'price' => 70000,
                'status' => 'occupied',
                'description' => 'Large booth in Hall B',
                'amenities' => json_encode(['electricity', 'wifi', 'water']),
            ],
            [
                'eventId' => $eventId,
                'name' => 'Booth B-203',
                'zone' => 'Hall B',
                'type' => 'booth',
                'size' => 'medium',
                'price' => 55000,
                'status' => 'available',
                'description' => 'Medium booth in Hall B',
                'amenities' => json_encode(['electricity', 'wifi']),
            ],
            [
                'eventId' => $eventId,
                'name' => 'Booth C-301',
                'zone' => 'Hall C',
                'type' => 'booth',
                'size' => 'xl',
                'price' => 85000,
                'status' => 'available',
                'description' => 'XL booth in Hall C',
                'amenities' => json_encode(['electricity', 'wifi', 'water', 'ac', 'storage']),
            ],
            [
                'eventId' => $eventId,
                'name' => 'Booth C-302',
                'zone' => 'Hall C',
                'type' => 'booth',
                'size' => 'xl',
                'price' => 85000,
                'status' => 'available',
                'description' => 'XL booth in Hall C',
                'amenities' => json_encode(['electricity', 'wifi', 'water', 'ac']),
            ],
            [
                'eventId' => $eventId,
                'name' => 'Booth Corner-K',
                'zone' => 'Entrance Hall',
                'type' => 'booth',
                'size' => 'xl',
                'price' => 120000,
                'status' => 'occupied',
                'description' => 'Premium corner booth at entrance',
                'amenities' => json_encode(['electricity', 'wifi', 'water', 'ac', 'signage']),
            ],
            [
                'eventId' => $eventId,
                'name' => 'Booth VIP-001',
                'zone' => 'VIP Zone',
                'type' => 'booth',
                'size' => 'xl',
                'price' => 150000,
                'status' => 'occupied',
                'description' => 'Exclusive VIP booth with premium services',
                'amenities' => json_encode(['electricity', 'wifi', 'water', 'ac', 'lounge', 'parking']),
            ],
        ];

        foreach ($spaces as $space) {
            Space::create($space);
        }
    }
}
