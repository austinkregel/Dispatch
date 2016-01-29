@extends(config('kregel.dispatch.view-base'))
@section('content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/vue/1.0.11/vue.js"></script>

<div class="container spark-screen">
    <div class="row">
        <div class="col-md-4">
            @include('dispatch::shared.menu')
        </div>
        <div class="col-md-8">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Hmmm... Looks like you belong to quite a few jurisdictions... Try selecting the one you want below
                </div>
                <div class="panel-body">
                    @include('dispatch::shared.errors')
                    <select>
                        <option disabled selected>Pleases select a Jurisdiction</option>
                        @foreach($jurisdictions as $jur)
                            <option value="{{$jur->id}}">{{ $jur->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection