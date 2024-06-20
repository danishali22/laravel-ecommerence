@extends('admin.layouts.app')


@section('content')
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit Coupon Code</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{ route('coupons.index') }}" class="btn btn-primary">Back</a>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="container-fluid">
            <form action="" method="post" name="couponForm" id="couponForm">
                {{-- @csrf --}}
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="code">Code</label>
                                    <input type="text" name="code" id="code" class="form-control"
                                        placeholder="Coupon Code" value="{{ $coupon->code }}">
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name">Name</label>
                                    <input type="text" name="name" id="name" class="form-control"
                                        placeholder="Coupon Code Name" value="{{ $coupon->name }}">
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="max_uses">Max Uses</label>
                                    <input type="text" name="max_uses" id="max_uses" class="form-control"
                                        placeholder="Max Users" value="{{ $coupon->max_uses }}">
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="max_uses_users">Max Users User</label>
                                    <input type="text" name="max_uses_users" id="max_uses_users" class="form-control"
                                        placeholder="Max Users for User" value="{{ $coupon->max_uses_users }}">
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="type">Type</label>
                                    <select name="type" id="type" class="form-control">
                                        <option value="percent" {{ $coupon->type == 'percent' ? 'selected' : '' }} >Percent</option>
                                        <option value="fixed" {{ $coupon->type == 'fixed' ? 'selected' : '' }} >Fixed</option>
                                    </select>
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="discount_amount">Discount Amount</label>
                                    <input type="number" name="discount_amount" id="discount_amount" class="form-control"
                                        placeholder="Discount Amount" value="{{ $coupon->discount_amount }}">
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="min_amount">Min Amount</label>
                                    <input type="number" name="min_amount" id="min_amount" class="form-control"
                                        placeholder="Min Amount" value="{{ $coupon->min_amount }}">
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="start_date">Start Date</label>
                                    <input type="text" name="start_date" id="start_date" class="form-control"
                                        placeholder="Start Date" autocomplete="off" value="{{ $coupon->starts_at }}">
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="end_date">End Date</label>
                                    <input type="text" name="end_date" id="end_date" class="form-control"
                                        placeholder="End Date" autocomplete="off" value="{{ $coupon->expires_at }}">
                                    <p></p>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status">Status</label>
                                    <select type="text" name="status" id="status" class="form-control">
                                        <option value="1" {{ ($coupon->status == '1' ? 'Active' : '') }}>Active</option>
                                        <option value="0" {{ ($coupon->status == '0' ? 'Block' : '') }}>Block</option>
                                    </select>
                                </div>
                            </div>
							<div class="col-md-6">
                                <div class="mb-3">
                                    <label for="description">Description</label>
                                    <textarea class="form-control" name="description" id="description" cols="10" rows="2">{{ $coupon->description }} </textarea>
                                    <p></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pb-5 pt-3">
                    <button class="btn btn-primary" type="submit">Update</button>
                    <a href="{{ route('coupons.index') }}" class="btn btn-outline-dark ml-3">Cancel</a>
                </div>
            </form>
        </div>
        <!-- /.card -->
    </section>
@endsection


@section('customJs')
    <script>
        $(document).ready(function() {
            $('#start_date').datetimepicker({
                // options here
                format: 'Y-m-d H:i:s',
            });

            $('#end_date').datetimepicker({
                // options here
                format: 'Y-m-d H:i:s',
            });
        });

        $('#couponForm').submit(function(event) {
            event.preventDefault();
            $("button[type=submit]").prop('disabled', true);
            var element = $(this);
            $.ajax({
                url: "{{ route('coupons.update', $coupon->id) }}",
                method: 'put',
                data: element.serializeArray(),
                dataType: 'json',
                success: function(response) {
                    $("button[type=submit]").prop('disabled', false);

                    let errors = response['error'];
                    if (response['status'] == true) {
                        window.location.href = "{{ route('coupons.index') }}";
                        $('#code').removeClass('is-invalid').siblings('p').removeClass(
                            'invalid-feedback').html("");
                        $('#discount_amount').removeClass('is-invalid').siblings('p').removeClass(
                            'invalid-feedback').html("");
                        $('#type').removeClass('is-invalid').siblings('p').removeClass(
                            'invalid-feedback').html("");
                        $('#status').removeClass('is-invalid').siblings('p').removeClass(
                            'invalid-feedback').html("");
                        $('#start_date').removeClass('is-invalid').siblings('p').removeClass(
                            'invalid-feedback').html("");
                        $('#end_date').removeClass('is-invalid').siblings('p').removeClass(
                            'invalid-feedback').html("");
                    } else {
                        if(response['notFound'] == true){
                        window.location.href = "{{route('coupons.index')}}";
                        return false;
                    }

                        let errors = response['errors'];
                        if (errors['code']) {
                            $('#code').addClass('is-invalid').siblings('p').addClass('invalid-feedback') .html(errors['code']);
                        } else {
                            $('#code').removeClass('is-invalid').siblings('p').removeClass( 'invalid-feedback').html("");
                        }

                        if (errors['discount_amount']) {
                            $('#discount_amount').addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors['discount_amount']);
                        } else {
                            $('#discount_amount').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
                        }
                        if (errors['status']) {
                            $('#status').addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors['status']);
                        } else {
                            $('#status').removeClass('is-invalid').siblings('p').removeClass( 'invalid-feedback').html("");
                        }
                        if (errors['type']) {
                            $('#type').addClass('is-invalid').siblings('p').addClass('invalid-feedback')
                                .html(errors['type']);
                        } else {
                            $('#type').removeClass('is-invalid').siblings('p').removeClass(
                                'invalid-feedback').html("");
                        }
                        if (errors['start_date']) {
                            $('#start_date').addClass('is-invalid').siblings('p').addClass('invalid-feedback')
                                .html(errors['start_date']);
                        } else {
                            $('#start_date').removeClass('is-invalid').siblings('p').removeClass(
                                'invalid-feedback').html("");
                        }
                        if (errors['end_date']) {
                            $('#end_date').addClass('is-invalid').siblings('p').addClass('invalid-feedback')
                                .html(errors['end_date']);
                        } else {
                            $('#end_date').removeClass('is-invalid').siblings('p').removeClass(
                                'invalid-feedback').html("");
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
