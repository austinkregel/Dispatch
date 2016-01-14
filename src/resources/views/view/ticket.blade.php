@extends(config('kregel.dispatch.view-base'))
@section('scripts')
    <script>
        setTimeout(function () {
            {{--var search = $('.make-comment input[name=comment]');--}}
                    {{--search.focus(function(e) {--}}
                    {{--var parent =       $(e.target).parent().parent().parent();--}}

                    {{--parent.css({--}}
                    {{--margin:'0 12px',--}}
                    {{--boxShadow:'0 12px 15px 0 rgba(0,0,0,0.24),0 17px 50px 0 rgba(0,0,0,0.19)'--}}
                    {{--});--}}
                    {{--if(!parent.hasClass('z-depth-4'))--}}
                    {{--parent.addClass('z-depth-4')--}}

                    {{--});--}}
                    {{--search.blur(function(e) {--}}
                    {{--var parent =       $(e.target).parent().parent().parent();--}}
                    {{--parent.css({--}}
                    {{--margin:'0 24px',--}}
                    {{--boxShadow:'0 2px 5px 0 rgba(0, 0, 0, 0.16), 0 2px 10px 0 rgba(0, 0, 0, 0.12)'--}}
                    {{--})--}}
                    {{--});--}}
                    {{--$('.ticket-comment').on('submit', function(e) {--}}
                    {{--e.preventDefault();--}}
                    {{--var Form = this;--}}

                    {{--var form = $(e.target),--}}
                    {{--action = e.target.action,--}}
                    {{--method = e.target.method;--}}
                    {{--//Save Form Data........--}}
                    {{--$.ajax({--}}
                    {{--cache: false,--}}
                    {{--url : action,--}}
                    {{--type: "POST",--}}
                    {{--dataType : "json",--}}
                    {{--headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },--}}
                    {{--data : form.serializeObject(),--}}
                    {{--context : Form,--}}
                    {{--success : function(callback){--}}
                    {{--//Where $(this) => context == FORM--}}
                    {{--$(this).parent().find('.response').html('success!');--}}
                    {{--$(this).val('');--}}
                    {{--},--}}
                    {{--error : function(){--}}
                    {{--$(this).parent().find('.response').html('<span>failure!</span>');--}}
                    {{--}--}}
                    {{--});--}}
                    {{--});--}}
                    $.fn.serializeObject = function () {
                var o = {};
                var a = this.serializeArray();
                $.each(a, function () {
                    if (o[this.name] !== undefined) {
                        if (!o[this.name].push) {
                            o[this.name] = [o[this.name]];
                        }
                        o[this.name].push(this.value || '');
                    } else {
                        o[this.name] = this.value || '';
                    }
                });
                return o;
            };


        }, 200);
    </script>
@endsection
@section('content')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/1.0.11/vue.js"></script>
    <div class="container spark-screen">
        <div class="row">
            <div class="col-md-4">
                @include('dispatch::shared.menu')
            </div>
            <div class="col-md-8 collapsible-container">
                <h3>Tickets for {{ $jurisdiction->name }}</h3>
                <ul class="collapsible popout" data-collapsible="accordion">
                    <?php
                    $color = '{"red_lighten_5":"ffebee","red_lighten_4":"ffcdd2","red_lighten_3":"ef9a9a","red_lighten_2":"e57373","red_lighten_1":"ef5350","red_base":"f44336","red_darken_1":"e53935","red_darken_2":"d32f2f","red_darken_3":"c62828","red_darken_4":"b71c1c","red_accent_1":"ff8a80","red_accent_2":"ff5252","red_accent_3":"ff1744","red_accent_4":"d50000","pink_lighten_5":"fce4ec","pink_lighten_4":"f8bbd0","pink_lighten_3":"f48fb1","pink_lighten_2":"f06292","pink_lighten_1":"ec407a","pink_base":"e91e63","pink_darken_1":"d81b60","pink_darken_2":"c2185b","pink_darken_3":"ad1457","pink_darken_4":"880e4f","pink_accent_1":"ff80ab","pink_accent_2":"ff4081","pink_accent_3":"f50057","pink_accent_4":"c51162","purple_lighten_5":"f3e5f5","purple_lighten_4":"e1bee7","purple_lighten_3":"ce93d8","purple_lighten_2":"ba68c8","purple_lighten_1":"ab47bc","purple_base":"9c27b0","purple_darken_1":"8e24aa","purple_darken_2":"7b1fa2","purple_darken_3":"6a1b9a","purple_darken_4":"4a148c","purple_accent_1":"ea80fc","purple_accent_2":"e040fb","purple_accent_3":"d500f9","purple_accent_4":"aa00ff","deep-purple_lighten_5":"ede7f6","deep-purple_lighten_4":"d1c4e9","deep-purple_lighten_3":"b39ddb","deep-purple_lighten_2":"9575cd","deep-purple_lighten_1":"7e57c2","deep-purple_base":"673ab7","deep-purple_darken_1":"5e35b1","deep-purple_darken_2":"512da8","deep-purple_darken_3":"4527a0","deep-purple_darken_4":"311b92","deep-purple_accent_1":"b388ff","deep-purple_accent_2":"7c4dff","deep-purple_accent_3":"651fff","deep-purple_accent_4":"6200ea","indigo_lighten_5":"e8eaf6","indigo_lighten_4":"c5cae9","indigo_lighten_3":"9fa8da","indigo_lighten_2":"7986cb","indigo_lighten_1":"5c6bc0","indigo_base":"3f51b5","indigo_darken_1":"3949ab","indigo_darken_2":"303f9f","indigo_darken_3":"283593","indigo_darken_4":"1a237e","indigo_accent_1":"8c9eff","indigo_accent_2":"536dfe","indigo_accent_3":"3d5afe","indigo_accent_4":"304ffe","blue_lighten_5":"e3f2fd","blue_lighten_4":"bbdefb","blue_lighten_3":"90caf9","blue_lighten_2":"64b5f6","blue_lighten_1":"42a5f5","blue_base":"2196f3","blue_darken_1":"1e88e5","blue_darken_2":"1976d2","blue_darken_3":"1565c0","blue_darken_4":"0d47a1","blue_accent_1":"82b1ff","blue_accent_2":"448aff","blue_accent_3":"2979ff","blue_accent_4":"2962ff","light-blue_lighten_5":"e1f5fe","light-blue_lighten_4":"b3e5fc","light-blue_lighten_3":"81d4fa","light-blue_lighten_2":"4fc3f7","light-blue_lighten_1":"29b6f6","light-blue_base":"03a9f4","light-blue_darken_1":"039be5","light-blue_darken_2":"0288d1","light-blue_darken_3":"0277bd","light-blue_darken_4":"01579b","light-blue_accent_1":"80d8ff","light-blue_accent_2":"40c4ff","light-blue_accent_3":"00b0ff","light-blue_accent_4":"0091ea","cyan_lighten_5":"e0f7fa","cyan_lighten_4":"b2ebf2","cyan_lighten_3":"80deea","cyan_lighten_2":"4dd0e1","cyan_lighten_1":"26c6da","cyan_base":"00bcd4","cyan_darken_1":"00acc1","cyan_darken_2":"0097a7","cyan_darken_3":"00838f","cyan_darken_4":"006064","cyan_accent_1":"84ffff","cyan_accent_2":"18ffff","cyan_accent_3":"00e5ff","cyan_accent_4":"00b8d4","teal_lighten_5":"e0f2f1","teal_lighten_4":"b2dfdb","teal_lighten_3":"80cbc4","teal_lighten_2":"4db6ac","teal_lighten_1":"26a69a","teal_base":"009688","teal_darken_1":"00897b","teal_darken_2":"00796b","teal_darken_3":"00695c","teal_darken_4":"004d40","teal_accent_1":"a7ffeb","teal_accent_2":"64ffda","teal_accent_3":"1de9b6","teal_accent_4":"00bfa5","green_lighten_5":"e8f5e9","green_lighten_4":"c8e6c9","green_lighten_3":"a5d6a7","green_lighten_2":"81c784","green_lighten_1":"66bb6a","green_base":"4caf50","green_darken_1":"43a047","green_darken_2":"388e3c","green_darken_3":"2e7d32","green_darken_4":"1b5e20","green_accent_1":"b9f6ca","green_accent_2":"69f0ae","green_accent_3":"00e676","green_accent_4":"00c853","light-green_lighten_5":"f1f8e9","light-green_lighten_4":"dcedc8","light-green_lighten_3":"c5e1a5","light-green_lighten_2":"aed581","light-green_lighten_1":"9ccc65","light-green_base":"8bc34a","light-green_darken_1":"7cb342","light-green_darken_2":"689f38","light-green_darken_3":"558b2f","light-green_darken_4":"33691e","light-green_accent_1":"ccff90","light-green_accent_2":"b2ff59","light-green_accent_3":"76ff03","light-green_accent_4":"64dd17","lime_lighten_5":"f9fbe7","lime_lighten_4":"f0f4c3","lime_lighten_3":"e6ee9c","lime_lighten_2":"dce775","lime_lighten_1":"d4e157","lime_base":"cddc39","lime_darken_1":"c0ca33","lime_darken_2":"afb42b","lime_darken_3":"9e9d24","lime_darken_4":"827717","lime_accent_1":"f4ff81","lime_accent_2":"eeff41","lime_accent_3":"c6ff00","lime_accent_4":"aeea00","yellow_lighten_5":"fffde7","yellow_lighten_4":"fff9c4","yellow_lighten_3":"fff59d","yellow_lighten_2":"fff176","yellow_lighten_1":"ffee58","yellow_base":"ffeb3b","yellow_darken_1":"fdd835","yellow_darken_2":"fbc02d","yellow_darken_3":"f9a825","yellow_darken_4":"f57f17","yellow_accent_1":"ffff8d","yellow_accent_2":"ffff00","yellow_accent_3":"ffea00","yellow_accent_4":"ffd600","amber_lighten_5":"fff8e1","amber_lighten_4":"ffecb3","amber_lighten_3":"ffe082","amber_lighten_2":"ffd54f","amber_lighten_1":"ffca28","amber_base":"ffc107","amber_darken_1":"ffb300","amber_darken_2":"ffa000","amber_darken_3":"ff8f00","amber_darken_4":"ff6f00","amber_accent_1":"ffe57f","amber_accent_2":"ffd740","amber_accent_3":"ffc400","amber_accent_4":"ffab00","orange_lighten_5":"fff3e0","orange_lighten_4":"ffe0b2","orange_lighten_3":"ffcc80","orange_lighten_2":"ffb74d","orange_lighten_1":"ffa726","orange_base":"ff9800","orange_darken_1":"fb8c00","orange_darken_2":"f57c00","orange_darken_3":"ef6c00","orange_darken_4":"e65100","orange_accent_1":"ffd180","orange_accent_2":"ffab40","orange_accent_3":"ff9100","orange_accent_4":"ff6d00","deep-orange_lighten_5":"fbe9e7","deep-orange_lighten_4":"ffccbc","deep-orange_lighten_3":"ffab91","deep-orange_lighten_2":"ff8a65","deep-orange_lighten_1":"ff7043","deep-orange_base":"ff5722","deep-orange_darken_1":"f4511e","deep-orange_darken_2":"e64a19","deep-orange_darken_3":"d84315","deep-orange_darken_4":"bf360c","deep-orange_accent_1":"ff9e80","deep-orange_accent_2":"ff6e40","deep-orange_accent_3":"ff3d00","deep-orange_accent_4":"dd2c00","brown_lighten_5":"efebe9","brown_lighten_4":"d7ccc8","brown_lighten_3":"bcaaa4","brown_lighten_2":"a1887f","brown_lighten_1":"8d6e63","brown_base":"795548","brown_darken_1":"6d4c41","brown_darken_2":"5d4037","brown_darken_3":"4e342e","brown_darken_4":"3e2723","grey_lighten_5":"fafafa","grey_lighten_4":"f5f5f5","grey_lighten_3":"eeeeee","grey_lighten_2":"e0e0e0","grey_lighten_1":"bdbdbd","grey_base":"9e9e9e","grey_darken_1":"757575","grey_darken_2":"616161","grey_darken_3":"424242","grey_darken_4":"212121","blue-grey_lighten_5":"eceff1","blue-grey_lighten_4":"cfd8dc","blue-grey_lighten_3":"b0bec5","blue-grey_lighten_2":"90a4ae","blue-grey_lighten_1":"78909c","blue-grey_base":"607d8b","blue-grey_darken_1":"546e7a","blue-grey_darken_2":"455a64","blue-grey_darken_3":"37474f","blue-grey_darken_4":"263238","black_base":"000000","white_base":"ffffff"}';
                    $colors = json_decode($color);
                    $i = 0;
                    ?>
                    @foreach($tickets as $ticket)

                        <?php $color = lighten('#'.config('kregel.dispatch.color'),
                                (1 - ($i / count($tickets))));//getClosest(hexdec(dechex(floor((abs(sin(hexdec(substr($ticket->title, 0, strlen($ticket->title)/2.5)))* 16777215)) % 16777215))) , $colors);?>
                        <li class="card">
                            <div class="card-title collapsible-header @if(config('app.debug')) themer--secondary @endif"
                                 style="background:#{{  $color }}">
                                <div class="card-title-custom">
                                    {{$ticket->title}} &mdash; Priority {{ $ticket->priority->name }}
                                    <div class="close" style="padding: 8px;">&times;</div>
                                </div>
                                <div style="width:calc(100% - 20px); margin:10px;line-height:2rem;">
                                    <span class="badge customize">Created: {{ date('M d, Y H:i', strtotime($ticket->created_at)) }}</span>
                                    <span class="badge customize">Tentative: {{ date('M d, Y', strtotime($ticket->created_at) + (strtotime($ticket->priority->deadline) - strtotime('now'))) }}</span>
                                    @if($ticket->comments->count() > 0)
                                        <span class="badge customize amber">{{ $ticket->comments->count() }} comments</span>
                                    @endif
                                    @if(!empty($ticket->closer->id))
                                        <span class="badge green customize">Closed</span>
                                    @elseif(($ticket->assigned_to->count()) > 0 )
                                        <span class="badge red customize" style="font-style:italic;">assigned</span>
                                    @elseif(empty($ticket->closer->id))
                                        <span class="badge blue customize" style="font-style:italic;">pending</span>
                                    @endif
                                </div>
                            </div>
                            <div class="collapsible-body @if(config('app.debug')) themer--accent-1 @endif"
                                 style="background-color:#{{ lighten($color, 0.3)}} !important;">
                                <p>
                                    {{$ticket->body}}
                                </p>
                                @if($ticket->assigned_to->count() > 0)
                                <div style="margin:30px;">
                                    This ticket is assigned to:
                                    <div class="col-md-12">
                                        <?php
                                        $users = $ticket->assigned_to;
                                        ?>
                                        @if( !empty($users))
                                            <?php
                                            $user_count = $users->count();
                                            $i = 1;
                                            ?>
                                            @foreach($users as $user)
                                                <a href="{{ route('dispatch::profile.user', [$user->id, str_slug($user->name)]) }}">{{ $user->name }}</a>{{ ($user_count > $i ? ', ' : '') }}
                                                <?php ++$i; ?>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                                @endif
                                <div style="padding:20px;">
                                    <ul class="card-wrapper collapsible popout" data-collapsible="accordion">

                                        @if($ticket->comments->count() > 0)
                                            <li style="margin:10px 22px;"><i>Replies...</i></li>
                                            <?php $comments = $ticket->comments()->orderBy('created_at',
                                                    'desc')->limit(5)->get()?>
                                            @foreach($comments as $comment)
                                                <li class="card " style="border-radius:5px; ">
                                                    <span class="card-title collapsible-header @if(config('app.debug')) themer--secondary @endif">
                                                        {{ $comment->user->name }}
                                                        <div class="close">&times;</div>
                                                        <div style="width:calc(100% - 20px);margin-top:-15px;margin-left:-5px;">
                                                            <span class="badge customize">Created: {{ date('M d, Y H:i', strtotime($comment->created_at)) . ' '. $comment->id }}</span>

                                                        </div>
                                                    </span>
                                                    <div class="collapsible-body @if(config('app.debug')) themer--accent-2 @endif">
                                                        <p>{{$comment->body}}</p>
                                                    </div>
                                                </li>
                                                <li style="height:1rem;width:100%;"></li>
                                            @endforeach

                                            <li style="padding:1rem;width:100%;"><a href="{{ route('dispatch::view.ticket-single', [
                                                str_slug($ticket->jurisdiction->name), $ticket->id
                                            ]) }}" style="color:#333">View more replies...</a></li>
                                        @endif
                                        <li class="card" style="background:transparent;box-shadow:none;">
										<span class="@if(config('app.debug')) themer--secondary @endif ticket-comment">
											<ticket-make-comment
                                                    :action="'{{ route('warden::api.create-model', ['comment']) }}'"
                                                    :ticket_id="'{{ $ticket->id }}'"
                                                    :user_id="'{{ Auth::user()->id }}'"
                                                    :_token="'{{ csrf_token() }}'"></ticket-make-comment>
										</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </li>
                        <?php ++$i;?>
                    @endforeach
                </ul>
            </div>
        </div>
@endsection