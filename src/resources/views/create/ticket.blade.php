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
                        File a ticket for {{ $jurisdiction->name }}
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
                                <input class="validate" id="_method" type="hidden" name="_method" value="">
                            </div>

                            <div class="input-field">
                                <input class="validate" id="_token" type="hidden" name="_token"
                                       value="yrd5Q3m6YJQBdFUQ6ye3GQ42AqUKuN7ljsEr5BUQ">
                            </div>

                            <div class="input-field">
                                <textarea class="materialize-textarea" id="title" type="text" name="title"
                                          v-model="data.title"></textarea>
                                <label for="title">Title</label>
                            </div>
                            <div class="input-field">
                                <textarea class="materialize-textarea" id="body" type="text" name="body"
                                          v-model="data.body"></textarea>
                                <label for="body">Body</label>
                            </div>
                            <div class="input-field">
                                <select id="priority_id" default="" type="select" name="priority_id"
                                        v-model="data.priority_id" @update="updateSelect">
                                <option value="" disabled selected>Please select a priority to assign this to</option>
                                @foreach(\Kregel\Dispatch\Models\Priority::all() as $priority)
                                    <option value="{{ $priority->id }}">{{ $priority->name }}</option>
                                @endforeach
                                </select>

                            </div>

                            <div class="input-field">
                                <select id="jurisdiction_id" type="select" name="jurisdiction_id" v-model="data.jurisdiction_id" @update="updateSelect">
                                <option value="" disabled>Please select a jurisdiction to assign this to</option>
                                @foreach(auth()->user()->jurisdiction as $jur)
                                    <option value="{{ $jur->id }}">{{$jur->name}}</option>
                                    @endforeach
                                    </select>
                            </div>

                            @if(auth()->user()->can_assign())
                            <div class="input-field">
                                <select multiple>
                                    <option value="" disabled selected>Please assign a user or two</option>
                                    @foreach([
                                        new Object(1, 'Austin')
                                    ] as $user)
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
@endsection
<?php
class Object{
    public $id = '';
    public $name = '';
    public function __construct ($id, $name){
        $this->id = $id;
        $this->name = $name;
    }
}?>