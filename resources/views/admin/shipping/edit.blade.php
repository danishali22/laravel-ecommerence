@extends('admin.layouts.app')


@section('content')

<section class="content-header">					
    <div class="container-fluid my-2">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Edit Shipping Charges</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{route('shipping.create')}}" class="btn btn-primary">Back</a>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->
</section>
<!-- Main content -->
<section class="content">
    <!-- Default box -->
    <div class="container-fluid">
    <form action="" method="POST" id="countryForm" name=countryForm>
        {{-- @csrf --}}
        <div class="card">
            <div class="card-body">								
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="name">Country</label>
                            <select name="country" id="country" class="form-control">
                                <option value="">Select Country</option>
                                @if ($countries->isNotEmpty())
                                    @foreach ($countries as $country)
                                    <option value="{{$country->id}}" {{$shipping->country_id == $country->id ? 'selected' : ''}} >{{$country->name}}</option>
                                    @endforeach
                                    <option value="rest_of_world" {{$shipping->country_id == 'rest_of_world' ? 'selected' : ''}} >Rest of the world</option>
                                @endif
                            </select>
                            <p></p>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="name">Amount</label>
                            <input type="text" name="amount" id="amount" class="form-control" placeholder="Amount" value={{$shipping->amount}}>	
                            <p></p>
                        </div>
                    </div>				
                </div>
            </div>							
        </div>
        <div class="pb-5 pt-3">
            <button class="btn btn-primary">Update</button>
            <a href="{{route('shipping.create')}}" class="btn btn-outline-dark ml-3">Cancel</a>
        </div>
    </form>
    </div>
    <!-- /.card -->
</section>

@endsection


@section('customJs')

<script>
$('#countryForm').submit(function() {
		event.preventDefault();
		$("button[type=submit]").prop('disabled', true);
		var element = $(this);
		$.ajax({
			url: '{{route("shipping.update", $shipping->id)}}',
			method: 'put',
			data: element.serializeArray(),
			dataType: 'json',
			success: function(response) {
				$("button[type=submit]").prop('disabled', false);

				let errors = response['error'];
				if (response['status'] == true) {
					window.location.href="{{route('shipping.create')}}";
					$('#country').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
					$('#amount').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
				} else {
                    if(response['notFound'] == true){
                        window.location.href = "{{route('shipping.create')}}";
                        return false;
                    }

					let errors = response['error'];
					if (errors['country']) {
						$('#country').addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors['error']);
					} else {
						$('#country').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
					}

					if (errors['amount']) {
						$('#amount').addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors['amount']);
					} else {
						$('#amount').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
					}
				}

			},
			error: function(jqXHR, exception) {
				console.log('Something went wrong');
			}
		})
	})


</script>

@endsection