@extends(config('kregel.dispatch.view-base'))
@section('content')
    <div class="col-md-12" style="padding:0;">
        <div class="jumbotron">
            <div class="container">
                <h2>Tickets for {{ $jurisdiction->name }}</h2>
            </div>
        </div>
    </div>
    <div class="container spark-screen">
        <div class="row">
            <div class="col-md-4">
                @include('dispatch::shared.menu')
            </div>
            <div class="col-md-8 collapsible-container">
                <div class="support__ticket__wrapper">
                    <?php
                    $i = 0;
                    $color = lighten('#' . config('kregel.dispatch.color'), 1 - ( $i / count($ticket) ));
                    //getClosest(hexdec(dechex(floor((abs(sin(hexdec(substr($ticket->title, 0, strlen($ticket->title)/2.5)))* 16777215)) % 16777215))) , $colors);?>
                    <div class="col-md-12 support__ticket" style="padding:0;margin:0;">
                        <div class="support__ticket__header col-md-12 @if(config('app.debug')) themer--secondary @endif" style="background:#{{ $color }}">
                            <div class="col-xm-2 col-sm-2 col-md-2 priority__wrapper">
                                <div class="priority__wrapper">
                                    <?php $totalPriorities = $ticket->priority->all()->count();?>
                                    @for($i=0;$i< $totalPriorities; $i++)
                                        @if($ticket->priority->stars > $i)
                                            <i class="fa fa-star" style="width:initial;margin-right:0.5rem;"></i>
                                        @else
                                            <i class="fa fa-star-o" style="width:initial;margin-right:0.5rem;"></i>
                                        @endif
                                    @endfor
                                </div>
                            </div>
                            <div class="col-xm-9 col-sm-9 col-md-9">
                                <div class="col-md-12">
                                    {{$ticket->title}}
                                </div>
                            </div>
                            <div class="col-md-12">
                                <span class="badge customize">Created: {{ date('M d, Y H:i', strtotime($ticket->created_at)) }}</span>
                                <span class="badge customize">Tentative: {{ date('M d, Y', strtotime($ticket->created_at) + (strtotime($ticket->priority->deadline) - strtotime('now'))) }}</span>
                                @if($ticket->comments->count() > 0)
                                    <span class="badge customize amber">{{ $ticket->comments->count() }} comments</span>
                                @endif
                                @if(!empty($ticket->closer->id))
                                    <span class="badge green customize">Closed</span>
                                @elseif(($ticket->assign_to->count()) > 0 )
                                    <span class="badge red customize" style="font-style:italic;">assigned</span>
                                @elseif(empty($ticket->closer->id))
                                    <span class="badge blue customize" style="font-style:italic;">pending</span>
                                @endif
                            </div>
                        </div>
                        <div class="support__ticket__body @if(config('app.debug')) themer--accent-1 @endif"
                             style="background-color:#{{ lighten($color, 0.3)}} !important;">
                            <p>
                                {{substr($ticket->body,0,150)}}
                            </p>
                            @if($ticket->assign_to->count() > 0)
                                <div style="margin:30px;">
                                    This ticket is assigned to:
                                    <div class="col-md-12">
                                        <?php
                                        $users = $ticket->assign_to;
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
                                        <?php $comments = $ticket->comments()->orderBy('created_at', 'desc')->limit(5)->get()?>
                                        @foreach($comments as $comment)
                                            <li class="" style="border-radius:5px; ">
                                                <span class="  active @if(config('app.debug')) themer--secondary @endif">
                                                    {{ $comment->user->name }}
                                                    <div class="close">&times;</div>
                                                    <div class="card-badge">
                                                        <span class="badge customize">Created: {{ date('M d, Y H:i', strtotime($comment->created_at)) . ' '. $comment->id }}</span>

                                                    </div>
                                                </span>
                                                <div class=" @if(config('app.debug')) themer--accent-2 @endif">
                                                    <p>{{$comment->body}}</p>
                                                </div>
                                            </li>
                                        @endforeach
                                    @endif

                                </ul>
                                <div style="padding:1rem;width:100%;"><a href="{{ route('dispatch::view.ticket-single', [
                                        str_slug($ticket->jurisdiction->name), $ticket->id
                                    ]) }}" style="color:#333">View more replies...</a></div>
                                <span class="@if(config('app.debug')) themer--secondary @endif ticket-comment">
                                    <ticket-make-comment
                                            :action="'{{ route('warden::api.create-model', ['comment']) }}'"
                                            :ticket_id="'{{ $ticket->id }}'"
                                            :user_id="'{{ Auth::user()->id }}'"
                                            :_token="'{{ csrf_token() }}'"></ticket-make-comment>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection