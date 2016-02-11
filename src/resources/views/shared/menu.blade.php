
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
                                    <i class="fa fa-btn fa-fw fa-cog"></i>New {{ $jur->name }}
                                    @if(!$jur->tickets->isEmpty())
                                        <span class="badge text-right" style="color:white;">{{ $jur->tickets->count( )}}</span>
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
                                <a href="{{ route('dispatch::new.jurisdiction', [str_slug($jur->name)]) }}"
                                   class="p-link">
                                    <i class="fa fa-btn fa-fw fa-cog"></i>New {{ $jur->name }}
                                    @if(!$jur->tickets->isEmpty())
                                        <span class="badge text-right" style="color:white;">{{ $jur->users->count( )}}</span>
                                    @endif
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>