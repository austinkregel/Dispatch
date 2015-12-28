<div class="panel panel-default panel-flush">
    <div class="panel-heading" style="text-transform:none;">
        Did something happen? File a ticket!
    </div>
    <div class="spark-panel-body panel-body">
        <div class="spark-settings-tabs">
            <ul class="nav-wrapper nav spark-settings-tabs-stacked" role="tablist">
                <?php
                $i = 2000;
                $dropdown = '';
                ?>
                @foreach(config('kregel.dispatch.models') as $menuitem => $classname)
                        <!-- Settings Dropdown -->
                <!-- Authenticated Right Dropdown -->
                <li class="dropdown">
                    <a href="#" class="dropdown-button" data-activates="side-bar-menu-{{ $i }}" >
                        {{ ucwords($menuitem) }} <i class="material-icons right">arrow_drop_down</i>
                    </a>
                    <?php
                    $dropdown .= '<ul class="dropdown-content" id="side-bar-menu-'.$i.'">
                        '.(auth()->user()->can('create-'.$menuitem) ? '<!-- Settings -->
                        <li>
                            <a href="'.route('dispatch::new.'.$menuitem).'" class="p-link">
                                <i class="fa fa-btn fa-fw fa-cog"></i>New '.ucwords($menuitem).'
                            </a>
                        </li>' : '').'
                        <li>
                            <a href="'.route('dispatch::view.'.$menuitem).'" class="p-link">
                                <i class="fa fa-btn fa-fw fa-cog"></i>List '.(Auth::user()->isAdmin() ? 'all ' : 'my ').ucwords($menuitem).'s
                            </a>
                        </li>
                    </ul>';
                    ++$i;
                    ?>
                </li>
                @endforeach
            </ul>
            {!! $dropdown !!}
        </div>
    </div>
</div>