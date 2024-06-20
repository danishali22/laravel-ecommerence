@extends('admin.layouts.app')

@section('content')
    
<section class="content-header">					
    <div class="container-fluid my-2">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Edit Product</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{route('products.index')}}" class="btn btn-primary">Back</a>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->
</section>
<!-- Main content -->
<section class="content">
    <!-- Default box -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <form method="post" action="" name="ProductsForm" id="ProductsForm"> 
                <div class="card mb-3">
                    <div class="card-body">								
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="title">Title</label>
                                    <input type="text" name="title" id="title" class="form-control" placeholder="Title" value="{{$products->title}}">	
                                    <p class="error"></p>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="title">Slug</label>
                                    <input type="text" name="slug" id="slug" class="form-control" placeholder="Slug" readonly value="{{$products->slug}}">	
                                    <p class="error"></p>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="description">Description</label>
                                    <textarea name="description" id="description" cols="30" rows="10" class="summernote" placeholder="Description" >{{$products->description}}</textarea>
                                </div>
                            </div> 
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="short_description">Short Description</label>
                                    <textarea name="short_description" id="short_description" cols="30" rows="10" class="summernote" placeholder="Short Description" >{{$products->short_description}}</textarea>
                                </div>
                            </div>                                            
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="shipping_returns">Shipping and Returns</label>
                                    <textarea name="shipping_returns" id="shipping_returns" cols="30" rows="10" class="summernote" placeholder="Shipping and Returns" >{{$products->shipping_returns}}</textarea>
                                </div>
                            </div>                                              
                        </div>
                    </div>	                                                                      
                </div>
                <div class="card mb-3">
                    <div class="card-body">
                        <h2 class="h4 mb-3">Media</h2>								
                        <div id="image" class="dropzone dz-clickable">
                            <div class="dz-message needsclick">    
                                <br>Drop files here or click to upload.<br><br>                                            
                            </div>
                        </div>
                    </div>	                                                                      
                </div>
                <div id="product_gallery" class="row">
                    @if ($productImage->isNotEmpty())
                        @foreach ($productImage as $image)
                        <div class="col-md-3">
                            <div class="card" id="image_row_{{$image->id}}">
                            <input type="hidden" name="imageArray[]" value={{$image->id}} >
                                <img src="{{asset('uploads/product/small/'.$image->image)}}" class="card-img-top" alt="Product Image">
                                <div class="card-body text-center">
                                    <a href="javascript:void(0)" onclick=deleteImage({{$image->id}}) class="btn btn-danger">Delete</a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @endif
                </div>
                <div class="card mb-3">
                    <div class="card-body">
                        <h2 class="h4 mb-3">Pricing</h2>								
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="price">Price</label>
                                    <input type="text" name="price" id="price" class="form-control" placeholder="Price" value="{{$products->price}}">	
                                    <p class="error"></p>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="compare_price">Compare at Price</label>
                                    <input type="text" name="compare_price" id="compare_price" class="form-control" placeholder="Compare Price" value="{{$products->compare_price}}">
                                    <p class="text-muted mt-3">
                                        To show a reduced price, move the product’s original price into Compare at price. Enter a lower value into Price.
                                    </p>	
                                </div>
                            </div>                                            
                        </div>
                    </div>	                                                                      
                </div>
                <div class="card mb-3">
                    <div class="card-body">
                        <h2 class="h4 mb-3">Inventory</h2>								
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="sku">SKU (Stock Keeping Unit)</label>
                                    <input type="text" name="sku" id="sku" class="form-control" placeholder="sku" value="{{$products->sku}}">	
                                    <p class="error"></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="barcode">Barcode</label>
                                    <input type="text" name="barcode" id="barcode" class="form-control" placeholder="Barcode" value="{{$products->barcode}}">	
                                </div>
                            </div>   
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <div class="custom-control custom-checkbox">
                                        <input type="hidden" name="track_qty" value="No">
                                        <input class="custom-control-input" {{$products->track_qty == 'Yes' ? 'checked' : ''}} type="checkbox" id="track_qty" name="track_qty" value="Yes">
                                        <label for="track_qty" class="custom-control-label">Track Quantity</label>
                                        <p class="error"></p>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <input type="number" min="0" name="qty" id="qty" class="form-control" placeholder="Qty" value="{{$products->qty}}">	
                                    <p class="error"></p>
                                </div>
                            </div>                                         
                        </div>
                    </div>	                                                                      
                </div>
            </div>
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-body">	
                        <h2 class="h4 mb-3">Product status</h2>
                        <div class="mb-3">
                            <select name="status" id="status" class="form-control">
                                <option value="1" {{$products->status == '1' ? 'selected' : ''}}>Active</option>
                                <option value="0" {{$products->status == '0' ? 'selected' : ''}}>Block</option>
                            </select>
                        </div>
                    </div>
                </div> 
                <div class="card">
                    <div class="card-body">	
                        <h2 class="h4  mb-3">Product category</h2>
                        <div class="mb-3">
                            <label for="category">Category</label>
                            <select name="category" id="category" class="form-control">
                                <option value="">Select Category</option>
                                @if ($categories->isNotEmpty())
                                    @foreach ($categories as $category)
                                        <option value="{{$category->id}}" {{$products->category_id == $category->id ? 'selected' : ''}}> {{$category->name}} </option>
                                    @endforeach
                                @endif
                            </select>
                            <p class="error"></p>
                        </div>
                        <div class="mb-3">
                            <label for="category">Sub category</label>
                            <select name="sub_category" id="sub_category" class="form-control">
                                <option value="">Select Sub Category</option>
                                @if ($subCategories->isNotEmpty())
                                    @foreach ($subCategories as $subCategory)
                                        <option value="{{$subCategory->id}}" {{$products->sub_category_id == $subCategory->id ? 'selected' : ''}}> {{$subCategory->name}} </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                </div> 
                <div class="card mb-3">
                    <div class="card-body">	
                        <h2 class="h4 mb-3">Product brand</h2>
                        <div class="mb-3">
                            <select name="brand" id="brand" class="form-control">
                                <option value="">Select Brand</option>
                                @if ($brands->isNotEmpty())
                                    @foreach ($brands as $brand)
                                        <option value="{{$brand->id}}" {{$products->brand_id == $brand->id ? 'selected' : ''}}>{{$brand->name}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                </div> 
                <div class="card mb-3">
                    <div class="card-body">	
                        <h2 class="h4 mb-3">Featured product</h2>
                        <div class="mb-3">
                            <select name="is_featured" id="is_featured" class="form-control">
                                <option value="No" {{$products->is_featured == 'No' ? 'selected' : ''}}>No</option>
                                <option value="Yes" {{$products->is_featured == 'Yes' ? 'selected' : ''}}>Yes</option>                                                
                            </select>
                            <p class="error"></p>
                        </div>
                    </div>
                </div>    
                <div class="card mb-3">
                    <div class="card-body">	
                        <h2 class="h4 mb-3">Related product</h2>
                        <div class="mb-3">
                            <select name="related_products[]" id="related_products" class="form-control related_products select2" multiple>   
                                @if (!empty($relatedProducts))
                                    @foreach ($relatedProducts as $relProduct)
                                        <option selected value="{{$relProduct->id}}" > {{ $relProduct->title }} </option>
                                    @endforeach
                                    
                                @endif                                
                            </select>
                            <p class="error"></p>
                        </div>
                    </div>
                </div>                                      
            </div>
        </div>
        
        <div class="pb-5 pt-3">
            <button class="btn btn-primary" type="submit">Update</button>
            <a href="{{route('products.index')}}" class="btn btn-outline-dark ml-3">Cancel</a>
        </div>
    </form>
    </div>
    <!-- /.card -->
</section>

@endsection

@section('customJs')

<script>
    $("#ProductsForm").submit(function(event){
        event.preventDefault();
        let formArray = $(this).serializeArray();
        $('button[type="submit"]').prop('disabled', true);
        $.ajax({
            url: '{{route("products.update",$products->id)}}',
            type: 'put',
            data: formArray,
            dataType: 'json',
            success: function(response){
                $('button[type="submit"]').prop('disabled', false);
                if(response['status'] == true){
                    $('.error').removeClass('invalid-feedback').html('');
                    $('input[type="text"], select').removeClass('is-invalid');
                    window.location.href="{{route('products.index')}}";
                }
                else{
                    let error = response['errors'];
                    $('.error').removeClass('invalid-feedback').html('');
                    $('input[type="text"], select').removeClass('is-invalid');
                    $.each(error, function(key, item){
                        $(`#${key}`).addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(item);
                    });
                }
            },
            error: function(){
                console.log('Something went wrong');
            }
        })
    });

    $("#category").change(function(event){
        var val = $(this).val();
        $.ajax({
            url: '{{route("products.subCategories")}}',
            type: 'get',
            data: {category_id: val},
            dataType: 'json',
            success: function(response){
               // console.log(response);
                $("#sub_category").find("option").not(":first").remove();
                $.each(response['subCategories'], function(key,item){
                    $('#sub_category').append(`<option value='${item.id}'> ${item.name} </option>`);
                });

            },
            error: function(){
                console.log('Something went wrong');
            }
        })
    });

    // Ajax Request for handling Slug
    $("#title").change(function(){
		$("button[type=submit]").prop('disabled', true);
		let element = $(this);
		$.ajax({
			url: '{{route("getSlug")}}',
			method: 'get',
			data: {title: element.val()},
			dataType: 'json',
			success: function(response) {
				$("button[type=submit]").prop('disabled', false);
				if(response['success'] == true){
					$("#slug").val(response['slug']); 
				}
			}
		})
	})

    Dropzone.autoDiscover = false;    
	const dropzone = $("#image").dropzone({ 
    url:  "{{ route('product-images.update') }}",
    maxFiles: 10,
    paramName: 'image',
    params: {product_id: {{$products->id}}},
    addRemoveLinks: true,
    acceptedFiles: "image/jpeg,image/png,image/gif",
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }, success: function(file, response){
        let html = `<div class="col-md-3">
                        <div class="card" id="image_row_${response.image_id}">
                        <input type="hidden" name="imageArray[]" value=${response.image_id} >
                            <img src="${response.image_path}" class="card-img-top" alt="Product Image">
                            <div class="card-body text-center">
                                <a href="javascript:void(0)" onclick=deleteImage(${response.image_id}) class="btn btn-danger">Delete</a>
                            </div>
                        </div>
                    </div>`

        $('#product_gallery').append(html);
    },
    complete: function(img){
        this.removeFile(img);
    }
});

function deleteImage(id){
    if(confirm('Are you sure you want to delete this Image?')){
        $("#image_row_"+id).remove();
        $('#product_gallery').append('');
        $.ajax({
            url: '{{route('product-images.delete')}}',
            type: 'delete',
            data: {id: id},
            success: function(response){
                if(response.status){
                    alert(response.message);
                }
            }
        });
        }
}

// Select2 dropdown
$('.related_products').select2({
    ajax: {
        url: '{{ route("products.getProducts") }}',
        dataType: 'json',
        tags: true,
        multiple: true,
        minimumInputLength: 3,
        processResults: function (data) {
            return {
                results: data.tags
            };
        }
    }
}); 
</script>
    
@endsection