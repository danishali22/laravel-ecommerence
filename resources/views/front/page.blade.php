@extends('front.layouts.app')

@section('content')

<main>
    <section class="section-5 pt-3 pb-3 mb-3 bg-white">
        <div class="container">
            <div class="light-font">
                <ol class="breadcrumb primary-color mb-0">
                    <li class="breadcrumb-item"><a class="white-text" href="{{route('front.home')}}">Home</a></li>
                    <li class="breadcrumb-item">{{ $page->name }}</li>
                </ol>
            </div>
        </div>
    </section>

    <section class=" section-10">
        <div class="container">
            <div class="section-title mt-5 ">
                <h2>{{ $page->name }}</h2>
            </div> 
            @if ($page->slug == "contact-us")
            <main>
            
                <section>
                    <div class="container">          
                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {!! Session::get('success') !!}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            </div>
                            <div class="col-md-6 mt-3 pe-lg-5">
                                {!! $page->content !!}                   
                            </div>
            
                            <div class="col-md-6">
                                <form class="shake" role="form" method="post" id="contactForm" name="contactForm">
                                    <div class="mb-3">
                                        <label class="mb-2" for="name">Name</label>
                                        <input class="form-control" id="name" type="text" name="name" data-error="Please enter your name">
                                        <p></p>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="mb-2" for="email">Email</label>
                                        <input class="form-control" id="email" type="email" name="email" data-error="Please enter your Email">
                                        <p></p>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="mb-2">Subject</label>
                                        <input class="form-control" id="subject" type="text" name="subject" data-error="Please enter your message subject">
                                        <p></p>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="message" class="mb-2">Message</label>
                                        <textarea class="form-control" rows="3" id="message" name="message" data-error="Write your message"></textarea>
                                        <p></p>
                                    </div>
                                  
                                    <div class="form-submit">
                                        <button class="btn btn-dark" type="submit" id="submit"><i class="material-icons mdi mdi-message-outline"></i> Send Message</button>
                                        <div id="msgSubmit" class="h3 text-center hidden"></div>
                                        <div class="clearfix"></div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </section>
            </main>
            @else
                {!!$page->content!!}
            @endif
        </div>
    </section>
</main>

@endsection

@section('customJs')

<script>

$("#contactForm").submit(function(event){
            event.preventDefault();
            $("#submit").prop('disabled', true);
            $.ajax({
                url: '{{route('front.sendContactEmail')}}',
                method: 'post',
			    data: $(this).serializeArray(),
                dataType: 'json',
                success: function(response){
                $("#submit").prop('disabled', false);
                    let errors = response.errors;
                    if(response.status == true){
                        $("#name").removeClass("is-invalid").siblings("p").removeClass("invalid-feedback").html("");
                        $("#email").removeClass("is-invalid").siblings("p").removeClass("invalid-feedback").html("");
                        $("#subject").removeClass("is-invalid").siblings("p").removeClass("invalid-feedback").html("");
                        window.location.href="{{route('front.pages', $page->slug)}}";
                    }
                    else{
                        if(errors.name){
                            $("#name").addClass("is-invalid").siblings("p").addClass("invalid-feedback").html(errors.name);
                        }
                        else{
                            $("#name").removeClass("is-invalid").siblings("p").removeClass("invalid-feedback").html("");
                        }
                        if(errors.email){
                            $("#email").addClass("is-invalid").siblings("p").addClass("invalid-feedback").html(errors.email);
                        }
                        else{
                            $("#email").removeClass("is-invalid").siblings("p").removeClass("invalid-feedback").html("");
                        }
                        if(errors.subject){
                            $("#subject").addClass("is-invalid").siblings("p").addClass("invalid-feedback").html(errors.subject);
                        }
                        else{
                            $("#subject").removeClass("is-invalid").siblings("p").removeClass("invalid-feedback").html("");
                        }
                    }
                }

            });
        });

</script>

@endsection