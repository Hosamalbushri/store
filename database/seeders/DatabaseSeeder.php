<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Attribute;
use App\Models\Category;
use App\Models\Order_Status;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

//         \App\Models\User::factory(10)->create();
        $statuses = [
            ['status_name' => 'الطلب تحت المعالجة','status_color'=>'info'],
            ['status_name' => 'تم  استلام  الطلب','status_color'=>'success'],
            ['status_name' => 'طلب مرفوض','status_color'=>'danger'],
        ];
        foreach ($statuses as $status) {
            Order_Status::create($status);
        }
        $attributes = [
            ['name' => 'الالوان','status'=>'1'],
            ['name' => 'الاحجام','status'=>'1'],
            ['name' => 'الاوزان','status'=>'1'],
            ['name' => 'الاطوال','status'=>'1'],
        ];
        foreach ($attributes as $attribute) {
            Attribute::create($attribute);
        }
//        Category::factory(10)->create();
//        Product::factory(10)->create();
    }
}
