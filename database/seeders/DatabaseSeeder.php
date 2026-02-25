<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create test admin user
        User::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('admin1234'),
            'role' => 'admin',
            'phone' => '+34 111 222 333',
            'address' => 'Test Admin Address',
        ]);

        // Create admin user for portfolio demo
        User::create([
            'name' => 'Admin Demo',
            'email' => 'admin@ladiabla.com',
            'password' => Hash::make('diabla2026'),
            'role' => 'admin',
            'phone' => '+34 123 456 789',
            'address' => 'Calle Principal 123, Madrid',
        ]);

        // Create customer user for testing
        User::create([
            'name' => 'Cliente Demo',
            'email' => 'cliente@ladiabla.com',
            'password' => Hash::make('cliente2026'),
            'role' => 'customer',
            'phone' => '+34 987 654 321',
            'address' => 'Avenida de la Constitución 45, Madrid',
        ]);

        // Create categories
        $pizzasCategory = Category::create([
            'name' => 'Pizzas',
            'description' => 'Deliciosas pizzas artesanales con ingredientes frescos',
            'is_active' => true,
        ]);

        $bebidasCategory = Category::create([
            'name' => 'Bebidas',
            'description' => 'Refrescantes bebidas para acompañar tu pizza',
            'is_active' => true,
        ]);

        $entradasCategory = Category::create([
            'name' => 'Entradas',
            'description' => 'Deliciosas entradas para comenzar',
            'is_active' => true,
        ]);

        $postresCategory = Category::create([
            'name' => 'Postres',
            'description' => 'Dulces postres para terminar tu comida',
            'is_active' => true,
        ]);

        // Create pizzas - Classic Argentine style from Il Napolitano
        Product::create([
            'category_id' => $pizzasCategory->id,
            'name' => 'Pizza Muzzarella',
            'description' => 'La clásica pizza argentina con abundante mozzarella y salsa de tomate casera',
            'price' => 8.99,
            'image' => 'products/pizza-muzzarella.png',
            'stock' => 50,
            'is_active' => true,
            'has_discount' => false,
        ]);

        Product::create([
            'category_id' => $pizzasCategory->id,
            'name' => 'Pizza Napolitana',
            'description' => 'Pizza con mozzarella, tomate en rodajas, ajo y orégano al estilo napolitano',
            'price' => 10.99,
            'image' => 'products/pizza-napolitana.png',
            'stock' => 45,
            'is_active' => true,
            'has_discount' => false,
        ]);

        Product::create([
            'category_id' => $pizzasCategory->id,
            'name' => 'Pizza 4 Quesos',
            'description' => 'Exquisita combinación de mozzarella, roquefort, provolone y parmesano',
            'price' => 12.99,
            'image' => 'products/pizza-4-quesos.png',
            'stock' => 40,
            'is_active' => true,
            'has_discount' => true,
            'discount_percentage' => 10,
        ]);

        Product::create([
            'category_id' => $pizzasCategory->id,
            'name' => 'Pizza de Albahaca',
            'description' => 'Pizza margarita con albahaca fresca, mozzarella de búfala y tomate',
            'price' => 11.99,
            'image' => 'products/pizza-de-albahaca.png',
            'stock' => 35,
            'is_active' => true,
            'has_discount' => false,
        ]);

        Product::create([
            'category_id' => $pizzasCategory->id,
            'name' => 'Pizza Jamón y Morrón',
            'description' => 'Pizza con jamón cocido, morrón asado y mozzarella',
            'price' => 11.50,
            'image' => 'products/pizza-jamon-morron.png',
            'stock' => 40,
            'is_active' => true,
            'has_discount' => false,
        ]);

        Product::create([
            'category_id' => $pizzasCategory->id,
            'name' => 'Pizza Rúcula y Jamón Crudo',
            'description' => 'Pizza con rúcula fresca, jamón crudo, parmesano y tomates cherry',
            'price' => 13.99,
            'image' => 'products/pizza-rucula-y-jamon-crudo.png',
            'stock' => 30,
            'is_active' => true,
            'has_discount' => true,
            'discount_percentage' => 15,
        ]);

        Product::create([
            'category_id' => $pizzasCategory->id,
            'name' => 'Fugazzeta',
            'description' => 'Pizza rellena con mozzarella y abundante cebolla caramelizada',
            'price' => 10.99,
            'image' => 'products/fugazzeta_cleanup.png',
            'stock' => 35,
            'is_active' => true,
            'has_discount' => false,
        ]);

        // Create bebidas
        Product::create([
            'category_id' => $bebidasCategory->id,
            'name' => 'Coca-Cola Zero 500ml',
            'description' => 'Refresco de cola sin azúcar',
            'price' => 2.50,
            'image' => 'products/coke-zero.jpg',
            'stock' => 100,
            'is_active' => true,
            'has_discount' => false,
        ]);

        Product::create([
            'category_id' => $bebidasCategory->id,
            'name' => 'Sprite 500ml',
            'description' => 'Refresco de lima-limón',
            'price' => 2.50,
            'image' => 'products/sprite.jpg',
            'stock' => 100,
            'is_active' => true,
            'has_discount' => false,
        ]);

        Product::create([
            'category_id' => $bebidasCategory->id,
            'name' => 'Agua Mineral 500ml',
            'description' => 'Agua mineral natural sin gas',
            'price' => 1.50,
            'image' => 'products/agua.jpg',
            'stock' => 150,
            'is_active' => true,
            'has_discount' => false,
        ]);

        Product::create([
            'category_id' => $bebidasCategory->id,
            'name' => 'Cerveza Quilmes 1L',
            'description' => 'Cerveza rubia argentina en botella de litro',
            'price' => 3.50,
            'image' => 'products/quilmes.jpg',
            'stock' => 80,
            'is_active' => true,
            'has_discount' => false,
        ]);

        // Create entradas - Argentine style
        Product::create([
            'category_id' => $entradasCategory->id,
            'name' => 'Empanadas Criollas (Docena)',
            'description' => '12 empanadas argentinas de carne, pollo o jamón y queso',
            'price' => 8.99,
            'image' => 'products/empanada.jpg',
            'stock' => 40,
            'is_active' => true,
            'has_discount' => false,
        ]);

        Product::create([
            'category_id' => $entradasCategory->id,
            'name' => 'Ensalada Mixta',
            'description' => 'Lechuga, tomate, cebolla, zanahoria y aceitunas con vinagreta',
            'price' => 5.99,
            'image' => 'products/ensalada.jpg',
            'stock' => 30,
            'is_active' => true,
            'has_discount' => false,
        ]);

        Product::create([
            'category_id' => $entradasCategory->id,
            'name' => 'Fainá',
            'description' => 'Tradicional fainá de harina de garbanzos, ideal con pizza',
            'price' => 4.50,
            'image' => 'products/faina.jpg',
            'stock' => 50,
            'is_active' => true,
            'has_discount' => true,
            'discount_percentage' => 15,
        ]);

        Product::create([
            'category_id' => $entradasCategory->id,
            'name' => 'Papas con Cheddar',
            'description' => 'Papas fritas caseras con abundante queso cheddar y panceta',
            'price' => 6.99,
            'image' => 'products/papas_con_cheddar.png',
            'stock' => 35,
            'is_active' => true,
            'has_discount' => false,
        ]);

        // Create postres
        Product::create([
            'category_id' => $postresCategory->id,
            'name' => 'Tiramisú',
            'description' => 'Postre italiano con café, mascarpone y cacao',
            'price' => 5.99,
            'image' => 'products/tiramisu.jpg',
            'stock' => 20,
            'is_active' => true,
            'has_discount' => false,
        ]);

        Product::create([
            'category_id' => $postresCategory->id,
            'name' => 'Brownie de Chocolate',
            'description' => 'Brownie casero con helado de vainilla',
            'price' => 4.99,
            'image' => 'products/brownie.avif',
            'stock' => 25,
            'is_active' => true,
            'has_discount' => false,
        ]);

        Product::create([
            'category_id' => $postresCategory->id,
            'name' => 'Helado Artesanal',
            'description' => 'Tres bolas de helado artesanal (chocolate, vainilla, fresa)',
            'price' => 4.50,
            'image' => 'products/helado.jpg',
            'stock' => 30,
            'is_active' => true,
            'has_discount' => false,
        ]);
    }
}

