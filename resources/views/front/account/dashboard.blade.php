@extends('front.layouts.app')

@section('content')
    <main>
        <div class="container mt-4">
            <div class="row">
                @if ($users->isNotEmpty())
                    <div class="col-md-3">
                        <ul class="list-group">
                            @foreach ($users as $user)
                                <li class="list-group-item list-group-item-dark user-list" data-id={{ $user->id }}> {{ $user->name }} </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="col-md-9">
                        <h3 class="start-head">CLick here to start the chat</h3>
                        
                        <div class="chat-section">

                            <div id="chat-container">
                                
                            </div>

                            <form id="chat-form" name="chat-form" method="POST">
                                <input type="text" name="message" id="message" class="border" placeholder="Enter Message" required>
                                <input type="submit" value="Send Message" class="btn btn-primary">
                            </form>
                        </div>
                    </div>
                @else
                    <div class="col-md-12">
                        <h5>Users not Found!!!</h5>
                    </div>
                @endif
            </div>
        </div>
    </main>
@endsection

@section('customJs')
    <script>
        $(document).ready(function(){
            $(".user-list").click(function(){

                $("#chat-container").html('');

                let getUserId = $(this).attr('data-id');
                receiver_id = getUserId;

                $(".start-head").hide();
                $(".chat-section").show();

                $("#chat-form").submit(function(e){
                    e.preventDefault();
                    let message = $("#message").val();

                    $.ajax({
                        url: "{{ route('account.saveChat') }}",
                        method: "POST",
                        data: {sender_id: sender_id, receiver_id: receiver_id, message: message},
                        dataType: "json",
                        success: function(response){
                            if(response.status == true){
                                $('#message').val('');

                                let chat = response.data.message;
                                let html = `
                                <div class="current-user-chat">
                                    <h5>${chat}</h5>
                                </div>
                                `;
                                $("#chat-container").append(html);
                            }
                            else{
                                alert(response.message);
                            }
                            
                        }
                    });
                })
            })
        })
    </script>

    <script>
        window.onload=function(){
        // console.log("ok");
        // Echo.private('broadcast-message').listen('.getChatMessage', (data) => {
        // alert(JSON.stringify(data));
        // let chat = response.data.message;
        // let html = `
        // <div class="other-user-chat">
        //     <h5>${chat}</h5>
        // </div>
        // `;
        // $("#chat-container").append(html);
        // });

           
        // Echo.channel(`broadcastMessage${this.senderId}`)
        // .listen('.getChatMessage', (e) => {
        //     console.log(e);
        // });
    }
    </script>
@endsection
