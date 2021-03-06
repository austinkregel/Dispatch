<div class="panel panel-default panel-flush">
    <div class="panel-heading @if(config('app.debug')) themer--secondary @endif" style="text-transform:none;">
        Did something happen? File a ticket!
    </div>
    <div class="spark-panel-body panel-body">
        <div class="spark-settings-tabs">
            <ul class="nav-wrapper nav spark-settings-tabs-stacked" role="tablist">
                <!-- Authenticated Right Dropdown -->
                @if(auth()->user()->can('create-ticket'))  <!-- Settings -->
                <li class="dropdown submenu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                       aria-expanded="false">Tickets <i class="fa fa-chevron-down"></i></a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="{{ route('dispatch::new.ticket') }}" class="p-link">
                                New Ticket
                            </a>
                        </li>
                        @foreach (auth()->user()->jurisdiction as $jur)
                            <li>
                                <a href="{{ route('dispatch::view.ticket', [str_slug($jur->name)]) }}"
                                   class="p-link">
                                    <i class="fa fa-btn fa-fw fa-cog"></i>View {{ $jur->name }}
                                    @if(!$jur->tickets->isEmpty())
                                        <span class="badge text-right" style="color:white;">{{ $jur->tickets()->where('deleted_at', NULL)->get()->count( )}}</span>
                                    @endif
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </li>
                @endif
                <li class="dropdown submenu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                       aria-expanded="false">Jurisdiction <i class="fa fa-chevron-down"></i></a>
                    <ul class="dropdown-menu">
                        @foreach (auth()->user()->jurisdiction as $jur)
                            <li>
                                <a href="{{ route('dispatch::edit.jurisdiction', [str_slug($jur->name)]) }}"
                                   class="p-link">
                                    <i class="fa fa-btn fa-fw fa-cog"></i>Edit {{ $jur->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>
@if(!empty($jurisdiction))
    <div class="panel panel-default panel-flush">
        <div class="panel-heading @if(config('app.debug')) themer--secondary @endif" style="text-transform:none;">
            Closed Tickets
        </div>
        <div class="spark-panel-body panel-body">
            <div class="spark-settings-tabs">
                <ul class="nav-wrapper nav spark-settings-tabs-stacked" role="tablist">
                    <!-- Authenticated Right Dropdown -->
                    @if(auth()->user()->can('create-ticket'))  <!-- Settings -->
                    <li class="dropdown submenu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                           aria-expanded="false">Tickets <i class="fa fa-chevron-down"></i></a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="{{ route('dispatch::new.ticket') }}" class="p-link">
                                    New Ticket
                                </a>
                            </li>
                            @foreach (auth()->user()->jurisdiction as $jur)
                                <li>
                                    <a href="{{ route('dispatch::view.ticket', [str_slug($jur->name)]) }}"
                                       class="p-link">
                                        <i class="fa fa-btn fa-fw fa-cog"></i>View {{ $jur->name }}
                                        @if(!$jur->tickets()->whereRaw('deleted_at is not null')->get()->isEmpty())
                                            <span class="badge text-right" style="color:white;">{{ $jur->tickets()->whereRaw('deleted_at is not null')->get()->count( )}}</span>
                                        @endif
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                    @endif

                </ul>
            </div>
        </div>
    </div>
@endif
<div style="width:100%;display:table;">
    <a href="@if(redirect()->back()) {{ redirect()->back()->getTargetUrl() }} @endif"class="col-lg-12 col-md-12 col-sm-12 col-xs-12 btn btn-primary" style="display:block;">Back</a>
</div>