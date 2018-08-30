<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Category;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Brian2694\Toastr\Facades\Toastr;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::latest()->get();
        return view('admin.category.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.category.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:categories',
            'image' => 'required|mimes:jpeg,bmp,png,jpg'
        ]);

        //get form image
        $image = $request->file('image');
        $slug = str_slug($request->name);
        
        if(isset($image))
        {
            //make unique name for image
            $currentDate = Carbon::now()->toDateString();
            $imagename = $slug.'-'.$currentDate.'-'.uniqid().'.'.$image->getClientOriginalExtension();

            //check category directory is exists
            if(!Storage::disk('public')->exists('category'))
            {
                Storage::disk('public')->makeDirectory('category');
            }

            //resize image for category and upload
            $category = Image::make($image)->resize(1600,479)->save();
            Storage::disk('public')->put('category/'.$imagename,$category);

            //check slider directory is exists
            if(!Storage::disk('public')->exists('slider/slider'))
            {
                Storage::disk('public')->makeDirectory('category/slider');
            }
            //resize image for category and upload
            $slider = Image::make($image)->resize(500,33)->save();
            Storage::disk('public')->put('category/slider/'.$imagename,$slider);
        } else {
            $imagename = "default.png";
         }

        $category = new Category();
        $category->name = $request->name;
        $category->slug = $slug;
        $category->image = $image;
        $category->save();

        Toastr::success('Category Sauccessfully Saved :)', 'Success');
        return redirect()->route('admin.category.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $category = Category::find($id);
        return view('admin.category.edit',compact('category'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required'
        ]);
        $category = Category::find($id);
        $category->name = $request->name;
        $category->slug = str_slug($request->name);

        if ($request->hasFile('image')) {

            $image = $request->image;
            $image_new_name = time().$image->getClientOriginalname();
            $image->move('uploads/category', $image_new_name);
            $category->image = 'uploads/category/' . $image_new_name;
        }
        $category->save();
        Toastr::success('Category Sauccessfully Updated :)', 'Success');
        return redirect()->route('admin.category.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}








