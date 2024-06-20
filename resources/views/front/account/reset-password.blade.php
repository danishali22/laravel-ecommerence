@extends('front.layouts.app')

@section('content')
    
<main>
    <section class="section-5 pt-3 pb-3 mb-3 bg-white">
        <div class="container">
            <div class="light-font">
                <ol class="breadcrumb primary-color mb-0">
                    <li class="breadcrumb-item"><a class="white-text" href="{{route('front.home')}}">Home</a></li>
                    <li class="breadcrumb-item">Reset Password</li>
                </ol>
            </div>
        </div>
    </section>

    <section class=" section-10">
        <div class="container">
            @if (Session::has('success'))
                    <div class="col-md-12">
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ Session::get('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                @endif
                @if (Session::has('error'))
                    <div class="col-md-12">
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ Session::get('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                @endif
            <div class="login-form">    
                <form action="{{route('front.processresetPassword')}}" method="post">
                    @csrf
                    <h4 class="modal-title">Reset Password</h4>
                    <input type="hidden" value="{{$token}}" name="token">
                    <div class="form-group">
                        <input type="password" class="form-control @error('new_password') is-invalid  @enderror" placeholder="New Password" name="new_password" id="new_password" value="{{old('new_password')}}">
                        @error('new_password')
                            <p class="invalid-feedback"> {{$message}} </p>
                        @enderror
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control @error('confirm_password') is-invalid  @enderror" placeholder="Confirm Password" name="confirm_password" id="confirm_password" value="{{old('confirm_password')}}">
                        @error('confirm_password')
                            <p class="invalid-feedback"> {{$message}} </p>
                        @enderror
                    </div>
                    <input type="submit" class="btn btn-dark btn-block btn-lg" value="Update Password">              
                </form>		
            </div>
        </div>
    </section>
</main>

@endsection

@section('customJs')
    
@endsection