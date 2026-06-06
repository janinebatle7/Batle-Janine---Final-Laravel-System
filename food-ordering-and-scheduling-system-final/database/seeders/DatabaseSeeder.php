<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\MenuItem;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

         // Seed dynamic Categories first
         Category::create(['name' => 'Meals']);
         Category::create(['name' => 'Drinks']);
         Category::create(['name' => 'Specials']);

        // 1. Seed Users (Admin, Staff, Customer)

        // Admin Account
        User::create([
            'name' => 'System Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        // Staff Account
        User::create([
            'name' => 'Kitchen Staff',
            'email' => 'staff@gmail.com',
            'password' => Hash::make('staff123'),
            'role' => 'staff',
        ]);

        // Customer Account
        User::create([
            'name' => 'janine Customer',
            'email' => 'customer@gmail.com',
            'password' => Hash::make('user123'),
            'role' => 'customer',
        ]);

        // 2. Seed Menu Items

        $items = [
            // MEALS
            [
                'name' => 'Classic Beast Burger',
                'description' => 'Double flame-grilled beef patties, melted sharp cheddar, caramelized onions, and our secret beast sauce on a brioche bun.',
                'price' => 250.00,
                'category' => 'Meals',
                'image' => 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?auto=format&fit=crop&q=80&w=800',
                'availability_status' => true,
            ],
            [
                'name' => 'Spicy Buffalo Wings',
                'description' => '8 pieces of crispy wings tossed in house-made habanero buffalo sauce. Served with blue cheese dip and celery sticks.',
                'price' => 195.00,
                'category' => 'Meals',
                'image' => 'https://images.unsplash.com/photo-1527477396000-e27163b481c2?auto=format&fit=crop&q=80&w=800',
                'availability_status' => true,
            ],
            [
                'name' => 'Truffle Mushroom Pasta',
                'description' => 'Creamy fettuccine with sautéed wild mushrooms, white truffle oil, and shaved parmesan cheese.',
                'price' => 320.00,
                'category' => 'Meals',
                'image' => 'https://images.unsplash.com/photo-1473093226795-af9932fe5856?auto=format&fit=crop&q=80&w=800',
                'availability_status' => true,
            ],

            // DRINKS
            [
                'name' => 'Iced Caramel Macchiato',
                'description' => 'Premium espresso shots with velvety milk and rich caramel drizzle over ice.',
                'price' => 145.00,
                'category' => 'Drinks',
                'image' => 'https://images.unsplash.com/photo-1485808191679-5f6333fef71f?auto=format&fit=crop&q=80&w=800',
                'availability_status' => true,
            ],
            [
                'name' => 'Wild Berry Smoothie',
                'description' => 'A refreshing blend of fresh strawberries, blueberries, and raspberries with a hint of honey.',
                'price' => 120.00,
                'category' => 'Drinks',
                'image' => 'https://images.unsplash.com/photo-1553531384-cc64ac80f931?auto=format&fit=crop&q=80&w=800',
                'availability_status' => true,
            ],

            // SPECIALS
            [
                'name' => 'Beast Platter for Two',
                'description' => 'The ultimate sharing experience. Includes 2 burgers, 6 wings, loaded fries, and two signature drinks.',
                'price' => 850.00,
                'category' => 'Specials',
                'image' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&q=80&w=800',
                'availability_status' => true,
            ],
            [
                'name' => 'Golden Rib-Eye Steak',
                'description' => 'Prime 400g rib-eye steak, butter-basted with rosemary and garlic. Served with mashed potatoes and seasonal greens.',
                'price' => 1250.00,
                'category' => 'Specials',
                'image' => 'https://images.unsplash.com/photo-1546241072-48010ad28c2c?auto=format&fit=crop&q=80&w=800',
                'availability_status' => true,
            ],
        ];

        foreach ($items as $item) {
            MenuItem::create($item);
        }
    }
}
