<?php

namespace App\Http\Controllers;

use App\Category;
use App\Product;
use App\Brand;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use mysql_xdevapi\Exception;

class WebController extends Controller
{
    public function demoRouting()
    {
        return view("layout");
    }

    public function login()
    {
        return view("login");
    }

    public function register()
    {
        return view("register");
    }

    public function index()
    {
        return view("home");
    }

    public function listCategory()
    {
        //Query builder

        $categories = DB::table("categories")->get();

        //Model (DRM)

        //show with condition

        //$categories = Category::where("catagory_name","LIKE","D%")->get();

        $categories = Category::withCount("Products")->paginate(20);
        return view("category.list",
            [
                "categories" => $categories
            ]);
    }

    public function newCategory()
    {
        return view("category.new");
    }

    public function saveCategory(Request $request)
    {
        $request->validate([
            "category_name" => "required|string|min:6|unique:categories"
        ]);
        try {
            Category::create([
                "category_name" => $request->get("category_name")
            ]); // return an Object of Category Model

//            DB::table("categories")-> insert([
//                "category_name"=> $request->get("category_name"),
//                "creared_at"=> Carbon::now(),
//                "updateed_at"=> Carbon::now(),
//            ]);
        } catch (\Exception $exception) {
            return redirect()->back();
        }
        return redirect()->to("/list-category");
    }

    public function editCategory($id)
    {
//        $category = Category::find($id);
//        if(is_null($category))
//            abort(404;

        $category = Category::findorfail($id);
        return view("category.edit", ["category" => $category]);
    }

    public function updateCategory($id, Request $request)
    {
        $category = Category::findorfail($id);
        $request->validate([
            "category_name" => "require|string|min:6|unique:categories,category_name,{$id}"]);
        try {
            $category->update()([
                "category_name" => $request->get("category_name")
            ]);
        } catch (\Exception $exception) {
            return redirect()->back();
        }
        return redirect()->to("/list-category");
    }

    public function deleteCategory($id)
    {
        $category = Category::findorfail($id);
        try {
            $category->delete();
        } catch (\Exception $exception) {
        }
        return redirect()->to("/list-category");
    }

    public function listProduct()
    {
//        $products = Product::leftJoin("categories", "categories_id", "=", "products.category_id")
////            ->leftjoin("brands", "brands_id", "=", "products.brand_id")
//            ->select("products.*", "categories.category_name", "category.id as category_id")
//            ->paginate(20);
        $products = Product::with("Category")->with("Brand")->paginate(20);
        return view("product.list", ["products" => $products]);
    }

    public function newProduct()
    {
        $categories = Category::all();
        $brands = Brand::all();

        return view("product.new", [
            "categories" => $categories,
           "brands" =>$brands,
        ]);
    }

    public function saveProduct(Request $request)
    {
        $request->validate([
            "product_name" => "required",
            "product_desc" => "required",
            "price" => "required|numeric|min:0",
            "qty" => "required|numeric|min:1",
            "category_id" => "required",
            "brand_id" => "required",
        ]);
        try {
            $productImage = null;
            // xử lý để đưa ảnh lên thư mục media trong public
            // sau đó lấy nguồn file cho vào biến productImage

            if ($request->hasFile("product_image")) {
                $file = $request->file("product_image");
                $allow = ["png", "jpg", "jpeg", "gif"];
                $extName = $file->getClientOriginalExtension(); // lay duoi file
                if (in_array($extName, $allow)) {
                    // get fileName
                    $fileName = time() . $file->getClientOriginalName();
                    //upload file into public/media
                    $file->move(public_path("media"), $fileName);
                    //convert string to productImage
                    $productImage = "media/" . $fileName;
                }

            }
            Product::create([
                "product_name" => $request->get("product_name"),
                "product_image" => $productImage,
                "product_desc" => $request->get("product_desc"),
                "price" => $request->get("price"),
                "qty" => $request->get("qty"),
                "category_id" => $request->get("category_id"),
                "brand_id" => $request->get("brand_id"),
            ]);
        } catch (\Exception $exception) {
            return redirect()->back();
        }
        return redirect()->to("/list-product");
    }
    public function editProduct($id, Request $request){
        $category = Category::all();
        $brand = Brand::all();
        $product = Product::findOrFail($id);
        return view("product.edit",[
            "categories"=>$category,
            "brands" => $brand,
            "products" => $product]);
    }


    public function updateProduct($id,Request $request){
        $product = Product::findOrFail($id);
        $request->validate([ // unique voi categories(table) category_name(truong muon unique), (id khong muon bi unique)
            "product_name" => "required|min:3|unique:products,product_name,{$id}",
            "product_desc" => "required",
            "price" => "required|numeric|min:0",
            "qty" => "required|numeric|min:1",
            "category_id" => "required",
            "brand_id" => "required",
        ]);
//            die("pass roi");
        try{
            $productImage = $product->get("product_image");
            // xử lý để đưa ảnh lên media trong public sau đó lấy nguồn file cho vào biến $product
            if($request->hasFile("product_image")){ // nếu request gửi lên có file product_image là inputname
                $file = $request->file("product_image"); // trả về 1 đối tượng lấy từ request của input
                // lấy tên file
                // thêm time() để thay đổi thời gian upload ảnh lên để không bị trùng ảnh với nhau
                $allow = ["png","jpg","jpeg","gif"];
                $extName = $file->getClientOriginalExtension();
                if(in_array($extName,$allow)){ // nếu đuôi file gửi lên nằm trong array
                    $fileName = time().$file->getClientOriginalName(); //  lấy tên gốc original của file gửi lên từ client
                    $file->move(public_path("media"),$fileName); // đẩy file vào thư mục media với tên là fileName
                    //convert string to ProductImage
                    $productImage = "media/".$fileName; // lấy nguồn file
                }
            }
            $product->update([
                "product_name" => $request->get("product_name"),
                "product_image" => $productImage,
                "product_desc" => $request->get("product_desc"),
                "price" => $request->get("price"),
                "qty" => $request->get("qty"),
                "category_id" => $request->get("category_id"),
                "brand_id" => $request->get("brand_id"),
            ]);
        }catch(Exception $exception){
            return redirect()->back();
        }
        return redirect()->to("/list-product");
    }

    public function deleteProduct($id)
    {
        $product = Product::findorFail($id);
        try {
            $product->delete();
        } catch (\Exception $exception) {
        }
        return redirect()->to("/list-product");
    }
}

