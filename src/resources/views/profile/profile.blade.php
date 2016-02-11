@extends(config('kregel.auth-login.base-layout'))
@section('content')
    @inject('menu', 'Kregel\Menu\Menu')
    <style>
        .user-profile-header {
            display: block;
            background: url(https://images.unsplash.com/photo-1445964047600-cdbdb873673d?ixlib=rb-0.3.5&q=80&fm=jpg&crop=entropy&s=c6665e8c83154faddeb6b9d253871486);
            background-size: cover;
            background-position: bottom;
            height: 400px;
            width: 100%;
        }

        .user-picture {
            z-index: 9;
            position: relative;
            margin-top: -15rem;
            height: 200px;
        }

        @media screen and (max-width: 990px) {
            .user-profile {
                height: 200px;
                background-position: center;
            }

            .user-picture {
                margin-top: 1rem;
                text-align: right;
                height: 0;
            }

            .user-picture:after {
                display: block;
                content: '';
                height: inherit;
            }
        }
    </style>
    <div class="user-profile-header"></div>
    <article class="container-fluid user-profile">
        <section>
            <article>
                <nav class="container-fluid">
                    <div class="container">
                        <ul class="col-md-8 col-md-offset-4 nav navbar-nav navbar-right">
                            {!! $menu->using('bootstrap')
                                 ->add([
                                     'Contact Me' => [
                                          'link' => 'mailto:' . $user->email,
                                    ],
                                    'Instant Message (coming soon)' => [
                                        'link' => ''
                                    ]
                                 ]) !!}
                        </ul>
                    </div>
                </nav>
            </article>
        </section>
        <section class="container ">
            <div class="col-md-4" style="">
                <aside class="user-picture center" style="">
                    <img src="https://secure.gravatar.com/avatar/{{md5($user->email)}}?d=identicon&s=200" alt=""
                         class="center z-depth-1">
                </aside>
                <h5>{{ ucwords($user->name) }}</h5>
                {{ strtolower($user->email) }}
                @if(!empty($user->contact_info))
                    There is contact info available for this user.
                @endif
                <h6>Jurisdictions</h6>
                <show-more-list
                        :data="{!! preg_replace('/\"/',"'",preg_replace("/^(?<!\\\)\'/", "\\'",$user->jurisdiction)) /*This converts a PHP array to a single quote array.*/ !!}"></show-more-list>
            </div>
            <div class="col-md-8 " style="margin-top:1rem;">
                <span class="grey-text lighten-2">Recent Tickets...</span>
                <ul class="collapsible popout" data-collapsible="accordion">
                    <?php
                    ?>
                    @foreach($tickets as $ticket)
                        <li class="card">
                            <div class="card-title collapsible-header @if(config('app.debug')) themer--secondary @endif">
                                <div style="width:calc(100% - 20px); margin:10px; padding-top:10px;border-bottom:solid thin grey;">
                                    {{$ticket->title}} &mdash; Priority {{ $ticket->priority->name }}
                                    <div class="close" style="padding: 8px;">&times;</div>
                                </div>
                                <div style="width:calc(100% - 20px); margin:10px;line-height:2rem ">
                                    <span class="badge customize">Created: {{ date('M d, Y H:i', strtotime($ticket->created_at)) }}</span>
                                    <span class="badge customize">Tentative: {{ date('M d, Y', strtotime($ticket->created_at) + (strtotime($ticket->priority->deadline) - strtotime('now'))) }}</span>
                                    @if($ticket->comments->count() > 0)
                                        <span class="badge customize amber">{{ $ticket->comments->count() }}
                                            comments</span>
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
                            <div class="collapsible-body @if(config('app.debug')) themer--accent-1 @endif">
                                <p>
                                    {{$ticket->body}}
                                </p>
                                @if($ticket->assign_to->count() > 0)
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
											<div style="width:calc(100% - 20px);">
                                                <span class="badge customize">Created: {{ date('M d, Y H:i', strtotime($comment->created_at)) . ' '. $comment->id }}</span>

                                            </div>
										</span>
                                                <div class="collapsible-body @if(config('app.debug')) themer--accent-2 @endif">
                                                    <p>{{$comment->body}}</p></div>
                                            </li>
                                            <li style="height:1rem;width:100%;"></li>
                                        @endforeach
                                    @endif
                                    <li class="card ">
										<span class="@if(config('app.debug')) themer--secondary @endif ticket-comment">
											<span class="response"></span>
											<ticket-comment
                                                    :action="'{{ route('warden::api.create-model', ['ticket-comment']) }}'"
                                                    :ticket_id="'{{ $ticket->id }}'"
                                                    :user_id="'{{ Auth::user()->id }}'"></ticket-comment>
										</span>
                                    </li>
                                </ul>

                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    </article>

@endsection