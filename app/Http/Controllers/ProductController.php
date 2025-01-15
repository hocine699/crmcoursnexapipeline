<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductBrand;
use App\Models\ProductCategory;
use App\Models\ProductTax;
use App\Models\Stream;
use App\Models\User;
use App\Exports\ProductExport;
use App\Imports\ProductImport;
use App\Models\UserDefualtView;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(\Auth::user()->can('Manage Product'))
        {
            if(\Auth::user()->type == 'owner')
            {
            $products = Product::where('created_by', \Auth::user()->creatorId())->get();
            $defualtView         = new UserDefualtView();
            $defualtView->route  = \Request::route()->getName();
            $defualtView->module = 'product';
            $defualtView->view   = 'list';
            User::userDefualtView($defualtView);
            }
            else
            {
                $products = Product::where('user_id', \Auth::user()->id)->get();

            $defualtView         = new UserDefualtView();
            $defualtView->route  = \Request::route()->getName();
            $defualtView->module = 'product';
            $defualtView->view   = 'list';
            }
            return view('product.index', compact('products'));
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(\Auth::user()->can('Create Product'))
        {
            $status = Product::$status;
            $user   = User::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            // $user->prepend('--', 0);
            $category = ProductCategory::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $brand    = ProductBrand::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $tax      = ProductTax::where('created_by', \Auth::user()->creatorId())->get()->pluck('tax_name', 'id');
            // $tax->prepend('No Tax', 0);

            return view('product.create', compact('user', 'category', 'status', 'brand', 'tax'));
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(\Auth::user()->can('Create Product'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'name' => 'required|max:120',
                                   'price' => 'required|numeric',
                                   'sku' => 'required',
                                   'tax' => 'required',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            if(count($request->tax) > 1 && in_array(0, $request->tax))
            {
                return redirect()->back()->with('error', 'Please select valid tax');
            }

            $product                = new Product();
            $product['user_id']     = $request->user;
            $product['name']        = $request->name;
            $product['status']      = $request->status;
            $product['category']    = $request->category;
            $product['brand']       = $request->brand;
            $product['price']       = $request->price;
            $product['tax']         = implode(',', $request->tax);
            $product['part_number'] = $request->part_number;
            $product['weight']      = $request->weight;
            $product['URL']         = $request->URL;
            $product['sku']            = $request->sku;
            $product['description'] = $request->description;
            $product['created_by']  = \Auth::user()->creatorId();
            $product->save();


            Stream::create(
                [
                    'user_id' => \Auth::user()->id,
                    'created_by' => \Auth::user()->creatorId(),
                    'log_type' => 'created',
                    'remark' => json_encode(
                        [
                            'owner_name' => \Auth::user()->username,
                            'title' => 'product',
                            'stream_comment' => '',
                            'user_name' => $product->name,
                        ]
                    ),
                ]
            );

            return redirect()->back()->with('success', __('Product Successfully Created.'));
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Product $product
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        if(\Auth::user()->can('Show Product'))
        {
            return view('product.view', compact('product'));
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Product $product
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        if(\Auth::user()->can('Edit Product'))
        {
            $user     = User::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            // $user->prepend('--', 0);
            $status   = Product::$status;
            $category = ProductCategory::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $brand    = ProductBrand::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $tax      = ProductTax::where('created_by', \Auth::user()->creatorId())->get()->pluck('tax_name', 'id');
            // $tax->prepend('No Tax', 0);

            // get previous user id
            $previous = Product::where('id', '<', $product->id)->max('id');
            // get next user id
            $next = Product::where('id', '>', $product->id)->min('id');

            return view('product.edit', compact('product', 'user', 'status', 'category', 'brand', 'tax', 'previous', 'next'));
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Product $product
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        if(\Auth::user()->can('Edit Product'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'name' => 'required|max:120',
                                   'price' => 'required|numeric',
                                   'sku'=>'required',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            if(count($request->tax) > 1 && in_array(0, $request->tax))
            {
                return redirect()->back()->with('error', 'Please select valid tax');
            }

            $product['user_id']     = $request->user;
            $product['name']        = $request->name;
            $product['status']      = $request->status;
            $product['category']    = $request->category;
            $product['brand']       = $request->brand;
            $product['price']       = $request->price;
            $product['tax']         = implode(',', $request->tax);
            $product['part_number'] = $request->part_number;
            $product['weight']      = $request->weight;
            $product['URL']         = $request->URL;
            $product['sku']            = $request->sku;
            $product['description'] = $request->description;

            $product['created_by']  = \Auth::user()->creatorId();
            $product->update();

            Stream::create(
                [
                    'user_id' => \Auth::user()->id,
                    'created_by' => \Auth::user()->creatorId(),
                    'log_type' => 'updated',
                    'remark' => json_encode(
                        [
                            'owner_name' => \Auth::user()->username,
                            'title' => 'product',
                            'stream_comment' => '',
                            'user_name' => $product->name,
                        ]
                    ),
                ]
            );

            return redirect()->back()->with('success', __('Product ' . $product->name . ' Successfully Updated.'));
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Product $product
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        if(\Auth::user()->can('Delete Product'))
        {
            $product->delete();

            return redirect()->back()->with('success', __('Product ' . $product->name . ' successfully deleted.'));
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    public function grid()
    {
        $products = Product::where('created_by', \Auth::user()->creatorId())->get();

        $defualtView         = new UserDefualtView();
        $defualtView->route  = \Request::route()->getName();
        $defualtView->module = 'product';
        $defualtView->view   = 'grid';
        User::userDefualtView($defualtView);

        return view('product.grid', compact('products'));
    }

     public function fileExport()
    {

        $name = 'product_' . date('Y-m-d i:h:s');
        $data = Excel::download(new ProductExport(), $name . '.xlsx');  ob_end_clean();


        return $data;
    }

       public function fileImportExport()
    {
        return view('product.import');
    }

    public function fileImport(Request $request)
    {

        $rules = [
            'file' => 'required|mimes:csv,txt,xlsx',
        ];

        $validator = \Validator::make($request->all(), $rules);

        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        $products = (new ProductImport())->toArray(request()->file('file'))[0];

        $totalproduct = count($products) - 1;

        $errorArray    = [];
        for($i = 1; $i <= count($products) - 1; $i++)
        {
            $product = $products[$i];

            $productByname = Product::where('name', $product[1])->first();
            

            if(!empty($productByname))
            {
                $productData = $productByname;
            }
            else
            {
                $productData = new Product();

            }


            $productData->user_id             = $product[0];
            $productData->name            = $product[1];
            $productData->status         = $product[2];
            $productData->category          = $product[3];
            $productData->brand          = $product[4]??'';
            $productData->price          = $product[5]??'';
            $productData->tax          = $product[6]??'';
            $productData->part_number     = $product[7];
            $productData->weight     = $product[8];
            $productData->URL     = $product[9];
            $productData->sku     = $product[10];
            $productData->description     = $product[11];
            $productData->created_by        = \Auth::user()->creatorId();



            if(empty($productData))
            {
                $errorArray[] = $productData;
            }
            else
            {
                $productData->save();
            }
        }

        $errorRecord = [];
        if(empty($errorArray))
        {
            $data['status'] = 'success';
            $data['msg']    = __('Record successfully imported');
        }
        else
        {
            $data['status'] = 'error';
            $data['msg']    = count($errorArray) . ' ' . __('Record imported fail out of' . ' ' . $totalproduct . ' ' . 'record');


            foreach($errorArray as $errorData)
            {

                $errorRecord[] = implode(',', $errorData);

            }

            \Session::put('errorArray', $errorRecord);
        }

        return redirect()->back()->with($data['status'], $data['msg']);
    }
}
