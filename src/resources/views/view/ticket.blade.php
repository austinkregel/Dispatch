@extends(config('kregel.dispatch.view-base'))
@section('scripts')
    <script>
        setTimeout(function () {
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
        }, 500);
    </script>
@endsection
@section('content')

    <div class="container spark-screen">
        <div class="row">
            <div class="col-md-4">
                @include('dispatch::shared.menu')
            </div>
            <div class="col-md-8 collapsible-container">
                <h3>Tickets for {{ $jurisdiction->name }}</h3>
                <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="false">
                    <?php $i=0;?>
                    @foreach($tickets as $ticket)

                        <?php $color = lighten('#'.config('kregel.dispatch.color'),
                                (1 - ($i / count($tickets))));//getClosest(hexdec(dechex(floor((abs(sin(hexdec(substr($ticket->title, 0, strlen($ticket->title)/2.5)))* 16777215)) % 16777215))) , $colors);?>
                        <div class="panel panel-default" style="margin:10px;">
                            <div class="panel-heading" style="color:#333;background-color:#{{  $color }}" role="tab" id="heading{{$ticket->id}}">
                                <h4 class="panel-title">
                                    <a class="collapsed" role="button" data-toggle="collapse"  data-parent="#accordion" href="#{{str_slug($ticket->title)}}" aria-expanded="false" ariacontrols="{{str_slug($ticket->title)}}">
                                        {{$ticket->title}} &mdash; Priority {{ $ticket->priority->name }}
                                    </a>
                                    <div class="ticket-action close-ticket">
                                        <a href="{{ route('dispatch::view.edit-ticket', [str_slug($ticket->jurisdiction->name), $ticket->id]) }}">
                                            <i class="fa fa-times"></i>
                                        </a>
                                    </div>
                                    <div class="ticket-action edit-ticket">
                                        <a href="{{ route('dispatch::view.edit-ticket', [str_slug($ticket->jurisdiction->name), $ticket->id]) }}">
                                            <i class="fa fa-pencil"></i>
                                        </a>
                                    </div>
                                </h4>
                                <div style="width:calc(100% - 20px); margin:10px;line-height:2rem;">
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
                            <div id="{{str_slug($ticket->title)}}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading{{$ticket->id}}"
                                 style="background-color:#{{ lighten($color, 0.3)}} !important;font-size:1.5rem">
                                <p>
                                    {{$ticket->body}}
                                </p>
                                @if($ticket->assign_to->count() > 0)
                                <div>
                                    This ticket is assigned to:
                                    <div class="col-md-12">

                                        @if(!$ticket->assign_to->isEmpty())
                                            <?php
                                            $user_count = $ticket->assign_to->count();
                                            $users = $ticket->assign_to;
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
                                <div class="panel-bottom">
                                    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="false">

                                        @if($ticket->comments->count() > 0)
                                            <div style="margin:10px 22px;"><i>Replies...</i></div>
                                            <?php $comments = $ticket->comments()->orderBy('created_at',
                                                    'desc')->limit(5)->get()?>
                                            <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="false">
                                            @foreach($comments as $comment)
                                                @include('dispatch::shared.comment', compact('color', 'comment'))
                                            @endforeach
                                            </div>

                                        @endif

                                    </div>
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
                        <?php ++$i;?>
                    @endforeach
                </div>
            </div>
        </div>

@endsection