@extends('admin.layouts.app')


@section('content')

<section class="content-header">
	<div class="container-fluid my-2">
		<div class="row mb-2">
			<div class="col-sm-6">
				<h1>Edit Brand</h1>
			</div>
			<div class="col-sm-6 text-right">
				<a href="{{route("brands.index")}}" class="btn btn-primary">Back</a>
			</div>
		</div>
	</div>
	<!-- /.container-fluid -->
</section>
<!-- Main content -->
<section class="content">
	<!-- Default box -->
	<div class="container-fluid">
		<form action="" method="post" name="brandForm" id="brandForm">
			{{-- @csrf --}}
			<div class="card">
				<div class="card-body">
					<div class="row">
						<div class="col-md-6">
							<div class="mb-3">
								<label for="name">Name</label>
								<input type="text" name="name" id="name" class="form-control" placeholder="Name" value="{{$brands->name}}">
								<p></p>
							</div>
						</div>
						<div class="col-md-6">
							<div class="mb-3">
								<label for="slug">Slug</label>
								<input type="text" name="slug" id="slug" class="form-control" placeholder="Slug" readonly value={{$brands->slug}}>
								<p></p>
							</div>
						</div>
						<div class="col-md-6">
							<div class="mb-3">
								<label for="status">Status</label>
								<select type="text" name="status" id="status" class="form-control">
									<option value="1" {{$brands->status == 1 ? 'selected' : ''}}>Active</option>
									<option value="0" {{$brands->status == 0 ? 'selected' : ''}}>Block</option>
								</select>
                                <p></p>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="pb-5 pt-3">
				<button class="btn btn-primary" type="submit">Update</button>
				<a href="{{route("brands.index")}}" class="btn btn-outline-dark ml-3">Cancel</a>
			</div>
		</form>
	</div>
	<!-- /.card -->
</section>

@endsection


@section('customJs')

<script>
	$('#brandForm').submit(function(event) {
		event.preventDefault();
		$("button[type=submit]").prop('disabled', true);
		var element = $(this);
		$.ajax({
			url: '{{route("brands.update", $brands->id)}}',
			method: 'put',
			data: element.serializeArray(),
			dataType: 'json',
			success: function(response) {
				$("button[type=submit]").prop('disabled', false);

				let errors = response['error'];
				if (response['status'] == true) {
					window.location.href="{{route('brands.index')}}";
					$('#name').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
					$('#slug').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
				} else {
                    if(response['notFound' == true]){
                        window.location.href = "{{route('brands.index')}}";
                    }
					let errors = response['error'];
					if (errors['name']) {
						$('#name').addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors['name']);
					} else {
						$('#name').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
					}

					if (errors['slug']) {
						$('#slug').addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors['slug']);
					} else {
						$('#slug').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
					}
				}

			},
			error: function(jqXHR, exception) {
				console.log('Something went wrong');
			}
		})
	})

	$("#name").change(function(){
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
</script>

@endsection