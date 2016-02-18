@extends(config('kregel.dispatch.view-base'))
@section('content')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/1.0.11/vue.js"></script>
    @include('dispatch::header', ['header' => 'Showing Jurisdictions'])
    <div class="container spark-screen">
        <div class="row">
            <div class="col-md-4">
                @include('dispatch::shared.menu')
            </div>
            <div class="col-md-8">
                <div class="panel panel-default">
                    <div class="panel-body">
                        @include('dispatch::shared.errors')
                        <table class="table" id="searchable">
                            <thead>
                            @foreach($jurisdiction->first()->getFillable() as $field)
                                {!! ((stripos($field, 'password')=== false) ?'<th>'.e($field).'</th>': '') !!}
                            @endforeach
                            {{--<th style="width:40px">Edit</th>--}}
                            {{--<th style="width:54px">Delete</th>--}}
                            </thead>
                            <tbody>
                            @foreach($jurisdiction as $model)
                                <tr>
                                    @foreach($jurisdiction->first()->getFillable() as $field)
                                        @if(stripos($field, 'password') === false)
                                            <td>
                                                @if(empty($model->$field))
                                                    <i>No data here</i>
                                                @else
                                                    {{$model->$field}}
                                                @endif
                                            </td>
                                        @endif
                                    @endforeach
                                    {{--<td>--}}
                                        {{--<span style="font-size:24px;">--}}
                                            {{--<a href="">--}}
                                                {{--<i class="@if(config('kregel.warden.using.fontawesome') === true) fa fa-edit @else glyphicon glyphicon-edit @endif"></i>--}}
                                            {{--</a>--}}
                                        {{--</span>--}}
                                    {{--</td>--}}
                                    {{--<td>--}}
                                        {{--<span style="text-align:right;float:right; font-size:24px;padding-right:10px;">--}}
                                        {{--<form action="" method='post'>--}}
                                            {{--<input type="hidden" name="_method" value="DELETE">--}}
                                            {{--@if(config('kregel.warden.using.csrf')) <input type="hidden" name="_token" value="{{csrf_token()}}">@endif--}}
                                            {{--<button type="submit" class="method-button"><i class="@if(config('kregel.warden.using.fontawesome') === true) fa fa-trash-o @else glyphicon glyphicon-trash @endif"></i></button>--}}
                                        {{--</form>--}}
                                    {{--</td>--}}
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        setTimeout(function(){
            var searchable = document.getElementById('searchable');
            if(searchable != undefined)
                (function($){
                    $('#searchable').DataTable();
                })(jQuery);
        }, 350)
    </script>
@endsection