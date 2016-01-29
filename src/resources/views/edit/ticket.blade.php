@extends(config('kregel.dispatch.view-base'))
@section('content')

    <div class="container spark-screen">
        <div class="row">
            <div class="col-md-4">
                @include('dispatch::shared.menu')
            </div>
            <div class="col-md-8">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Edit the ticket for {{ $jurisdiction->name }}
                    </div>
                    <div class="panel-body">
                        @include('dispatch::shared.errors')
                        <div id="vue-form-wrapper">
                            <div id="response" v-show="response">
                                @{{ response }}
                                <div class="close" @click="close">&times;</div>
                        </div>
                        <form @submit.prevent="makeRequest" method="POST" enctype="multipart/form-data"
                              action="http://theateradmin.dev/warden/api/v1.0/ticket">
                            <div class="input-field">
                                <input class="validate" id="_method" type="hidden" name="_method" value="put">
                            </div>

                            {!! csrf_field() !!}

                            <div class="input-field">
                                <textarea class="materialize-textarea" id="title" type="text" name="title"
                                          v-model="data.title" value="{{ $ticket->title }}"></textarea>
                                <label for="title">Title</label>
                            </div>
                            <div class="input-field">
                                <textarea class="materialize-textarea" id="body" type="text" name="body"
                                          v-model="data.body"  value="{{ $ticket->body }}"></textarea>
                                <label for="body">Body</label>
                            </div>
                            <div class="input-field">
                                <select id="priority_id" default="" type="select" name="priority_id"
                                        v-model="data.priority_id" @update="updateSelect">
                                <option value="" disabled selected>Please select a priority to assign this to</option>
                                @foreach(\Kregel\Dispatch\Models\Priority::all() as $priority)
                                    <option value="{{ $priority->id }}" @if($ticket->priority->name === $priority->name) selected@endif>{{ $priority->name }}</option>
                                @endforeach
                                </select>

                            </div>
                            @if(empty($jurisdiction))
                                <div class="input-field">
                                    <select multiple id="jurisdiction">
                                        <option value="" disabled selected>Please assign this to a location</option>
                                        @foreach(auth()->user()->jurisdiction as $jurisdiction)
                                            <option value="{{ $jurisdiction->id }}">{{ $jurisdiction->name }}</option>
                                        @endforeach

                                    </select>
                                </div>
                            @endif
                            @if(auth()->user()->can_assign())
                            <div class="input-field">
                                <select multiple id="assign_to">
                                    <option value="" disabled selected>Please assign a user or two</option>
                                    @foreach($jurisdiction->users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach

                                </select>
                            </div>


                            @endif
                            <div class="input-field">
                                <div class="input-field">
                                    <input class="btn waves-effect waves-light" id="" type="submit">
                                </div>
                            </div>
                    </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
    <script>
        setTimeout(function(){
            if(typeof $ !== undefined){
                @if(auth()->user()->can_assign())
                    $('#assign_to').material_select();
                @endif
                $('#assign_to').material_select();
            }
        }, 500);
    </script>
@endsection