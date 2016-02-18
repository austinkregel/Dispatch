@extends(config('kregel.dispatch.view-base'))
@section('content')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/1.0.11/vue.js"></script>
    @include('dispatch::header', ['header' => 'Edit '. $jurisdiction->name])

    <div class="container spark-screen">
        <div class="row">
            <div class="col-md-4">
                @include('dispatch::shared.menu')
            </div>
            <div class="col-md-8">
                <div class="panel panel-default">
                    <div class="panel-body">
                        @include('dispatch::shared.errors')
                        {!! $form !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection