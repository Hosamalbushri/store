<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class ProductController extends Controller
{
   public function index()
   {
       $products=Product::with('subcategory','skus')->active()->get();
       $updatedProducts = $products->map(function ($product) {
           return [
               'product_id' => $product->id,
               'product_name'=>$product->name,
               'product_description'=>$product->description,
               'photo'=>$product->photo(),
               'category'=>[

                       'id'=>$product->subcategory->category->id,
                       'name'=>$product->subcategory->category->name,
                       'sub_category'=>
                       [
                           'id'=>$product->subcategory->id,
                           'name'=>$product->subcategory->name
                       ]
               ],
               'skus'=>$product->skus->map(function($item){
                   return [
                       'id'=>$item->id
                       ,'price'=>$item->price,
                       'code'=>$item->code,
                       'discount'=>[
                           'value'=>isset($item->discount->value)?$item->discount->value:null,
                           'discounted_price'=>isset($item->discount->discounted_price)?$item->discount->discounted_price:null

                       ],
                       'attribute'=>$item->attribute_Options->map(function($item){
                           return[
                               'main_attribute'=>isset($item->attribute->name)?$item->attribute->name:'null',
                               'value' => isset($item->value)?$item->value:'null',
                               'id'=>isset($item->id)?$item->id:'null',
                               'color'=>isset($item->values)?$item->values->value:'null'

                           ];

                       }),
                   ];

               })
           ];
       });

       return response()->json($updatedProducts);

       }


    public function detail($id)
    {
        $products = Product::with('subcategory','skus')->active()->find($id);
        $products =([
            'product_id'=>$products->id,
            'product_name'=>$products->name,
            'product_description'=>$products->description,
            'photo'=>$products->photo(),
        'category'=>
            [
                    'id'=>$products->subcategory->category->id,
                    'name'=>$products->subcategory->category->name
                    ,'sub_category'=>
                    [
                        'id'=>$products->subcategory->id,
                        'name'=>$products->subcategory->name
                    ]
            ],
            'skus'=>$products->skus->map(function($item){
                return [
                    'id'=>$item->id,
                    'price'=>$item->price,
                    'code'=>$item->code,
                    'discount'=>[
                        'value'=>isset($item->discount->value)?$item->discount->value:null,
                        'discounted_price'=>isset($item->discount->discounted_price)?$item->discount->discounted_price:null

                    ],                    'attribute'=>$item->attribute_Options->map(function($item){
                       return[
                           'main_attribute'=>isset($item->attribute->name)?$item->attribute->name:'null',
                           'value' => isset($item->value)?$item->value:'null',
                           'id'=>isset($item->id)?$item->id:'null',
                           'color'=>isset($item->values)?$item->values->value:'null'

                       ];

                    }),
                ];

            }),


    ]);


        if(!$products){
            return response()->json(['message' => 'هذا المنتج غير موجود اوقد يكون محذوفا'], 404);
        }
        return response()->json($products);

    }
    public function categories()
    {
        $categories=Category::with('Subcategoreies')->active()->get();
        if (!$categories)
        {
            return response()->json(['message' => 'هذا القسم غير موجود اوقد يكون محذوفا'], 404);
        }
        $category=$categories->map(function($item){
            return
            [
                'id'=>$item->id,
                'name'=>$item->name,
                'sub_categories'=>isset($item->Subcategoreies)?$item->Subcategoreies->map(function($item){
                    return
                    [
                        'id'=>$item->id,
                        'name'=>$item->name,
                    ];

                }):'null'
            ];
        });
        return response()->json($category);

    }

    public function all_products($id)
    {
        $subcategories=SubCategory::with('products')->where('status',true)->find($id);
        if (!$subcategories)
        {
            return response()->json(['message' => 'هذا القسم غير موجود اوقد يكون محذوفا'], 404);
        }
        if (isset($subcategories->products))
        {
            $subcategory=$subcategories->products->map(function($item){
                return
                    [

                        'product_id'=>$item->id,
                        'product_name'=>$item->name,
                        'product_description'=>$item->description,
                        'photo'=>$item->photo(),
                        'category'=>[
                            'id'=>$item->subcategory->category->id,
                            'name'=>$item->subcategory->category->name,
                            'sub_category'=>[
                                'id'=>$item->subcategory->id,
                                'name'=>$item->subcategory->name,

                            ]
                        ],
                        'skus'=>$item->skus->map(function($item){
                            return [
                                'id'=>$item->id,
                                'price'=>$item->price,
                                'code'=>$item->code,
                                'discount'=>[
                                    'value'=>isset($item->discount->value)?$item->discount->value:null,
                                    'discounted_price'=>isset($item->discount->discounted_price)?$item->discount->discounted_price:null

                                ],                                'attribute'=>$item->attribute_Options->map(function($item){
                                    return[
                                        'main_attribute'=>isset($item->attribute->name)?$item->attribute->name:'null',
                                        'value' => isset($item->value)?$item->value:'null',
                                        'id'=>isset($item->id)?$item->id:'null',
                                        'color'=>isset($item->values)?$item->values->value:'null'

                                    ];

                                }),
                            ];

                        }),

                    ];

            });

            return response()->json($subcategory);


        }






    }

    public function category_products($category_id,$subcategory_id)
    {
        $category=Category::active()->find($category_id);
        $subcategories=SubCategory::with('products')->where('status',true)->find($subcategory_id);

        if (!$category||!$subcategories)
        {
            return response()->json(['message' => 'هذا القسم غير موجود اوقد يكون محذوفا'], 404);
        }
        if ($category->id!=$subcategories->category->id)
        {
            return response()->json(['message' => 'هذا القسم الفرعي غير تابع لهذا القسم الرئيسي'], 404);
        }
        if (isset($subcategories->products))
        {
            $category= $subcategories->products->map(function($item){
                return
                    [
                        'product_id'=>$item->id,
                        'product_name'=>$item->name,
                        'product_description'=>$item->description,
                        'photo'=>$item->photo(),
                        'category'=>[
                            'id'=>$item->subcategory->category->id,
                            'name'=>$item->subcategory->category->name,
                            'sub_category'=>[
                                'id'=>$item->subcategory->id,
                                'name'=>$item->subcategory->name,

                            ]
                        ],
                        'skus'=>$item->skus->map(function($item){
                            return [
                                'id'=>$item->id,
                                'price'=>$item->price,
                                'code'=>$item->code,
                                'discount'=>[
                                    'value'=>isset($item->discount->value)?$item->discount->value:null,
                                    'discounted_price'=>isset($item->discount->discounted_price)?$item->discount->discounted_price:null

                                ],                                'attribute'=>$item->attribute_Options->map(function($item){
                                    return[
                                        'main_attribute'=>isset($item->attribute->name)?$item->attribute->name:'null',
                                        'value' => isset($item->value)?$item->value:'null',
                                        'id'=>isset($item->id)?$item->id:'null',
                                        'color'=>isset($item->values)?$item->values->value:'null'

                                    ];

                                }),
                            ];

                        }),

                    ];

            });

                return response()->json($category);


        }

    }
    public function category($id)
    {
        $category=Category::with('Subcategoreies')->active()->find($id);
        if (!$category)
        {
            return response()->json(['message' => 'هذا القسم غير موجود اوقد يكون محذوفا'], 404);
        }
        $category=[
            'id'=>$category->id,
            'name'=>$category->name,
            'subcategories'=>isset($category->Subcategoreies)?$category->Subcategoreies->map(function($item){
                return
                [
                    'id'=>$item->id,
                    'name'=>$item->name,
                ];
            }):'null'

        ];
        return response()->json($category);

    }


}
