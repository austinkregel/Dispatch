@extends(config('kregel.dispatch.view-base'))
@section('content')
    <script src="https://code.jquery.com/jquery-1.12.0.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.5/js/materialize.min.js"></script>
    @include('dispatch::header', ['header' => 'Showing Jurisdictions'])

    <div class="container spark-screen">
        <div class="row">
            <div class="col-md-4">
                @include('dispatch::shared.menu')
            </div>
            <div class="col-md-8">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        @if(!empty($jurisdiction))
                            File a ticket for {{ $jurisdiction->name }}
                        @else
                            File a ticket
                        @endif

                    </div>
                    <div class="panel-body">
                        @include('dispatch::shared.errors')
                        <div id="vue-form-wrapper">
                            <div id="response" v-show="response" :class="responseClasses">
                                @{{ response }}
                                <div class="close" @click="close">&times;</div>
                        </div>
                        <form method="POST" enctype="multipart/form-data"
                              action="{{ route('warden::api.create-model', ['ticket']) }}">
                            <div class="form-group">
                                <input class="form-control" id="_method" type="hidden" name="_method" value="post">
                            </div>
                            <div class="form-group">
                                <input class="form-control" id="_redirect" type="hidden" name="_redirect" value="{{ route('dispatch::view.ticket', [str_slug($jurisdiction->name)]) }}">
                            </div>
                            <div class="form-group">
                                <input class="form-control" v-model="data.owner_id" id="owner_id" type="hidden"
                                       name="owner_id" value="{{ auth()->user()->id }}">
                            </div>
                            <div class="form-group">
                                <input class="form-control" v-model="data.jurisdiction_id" id="jurisdiction_id"
                                       type="hidden" name="jurisdiction_id" value="{{ $jurisdiction->id }}">
                            </div>
                            {!! csrf_field() !!}

                            <div class="form-group">
                                <label for="title">Title</label>

                                <textarea class="form-control" cols="3" id="title" type="text" name="title"
                                          v-model="data.title"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="body">Body</label>

                                <textarea class="form-control" cols="3" id="body" type="text" name="body"
                                          v-model="data.body"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="finish_by">Finish by (optional)</label>

                                <input class="form-control" id="finish_by" type="datetime" name="finish_by"
                                          v-model="data.finish_by">
                            </div>
                            <div class="form-group">
                                <select id="priority_id" default="" type="select" name="priority_id"
                                        v-model="data.priority_id" class="form-control">
                                    <option value="" disabled selected>Please select a priority to assign this to
                                    </option>
                                    @foreach(\Kregel\Dispatch\Models\Priority::all() as $priority)
                                        <option value="{{ $priority->id }}">{{ $priority->name }}</option>
                                    @endforeach
                                </select>

                            </div>
                            @if(empty($jurisdiction))
                                <div class="form-group">
                                    <select id="jurisdiction" v-model="data.jurisdiction_id">
                                        <option value="" disabled selected>Please assign this to a location</option>
                                        @foreach(auth()->user()->jurisdiction as $jurisdiction)
                                            <option value="{{ $jurisdiction->id }}">{{ $jurisdiction->name }}</option>
                                        @endforeach

                                    </select>
                                </div>
                            @endif
                            @if(auth()->user()->can_assign())
                                <div class="form-group">
                                    <select multiple id="assign_to" class="form-control" v-model="data.assign_to">
                                        <option value="" disabled selected>Please assign a user or two</option>
                                        @foreach($jurisdiction->users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>


                            @endif
                            <div class="form-group">
                                <div class="form-group">
                                    <input class="btn btn-primary pull-right" id="" type="submit">
                                </div>
                            </div>

                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>@endsection