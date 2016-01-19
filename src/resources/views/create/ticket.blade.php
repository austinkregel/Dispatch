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
                        @include('formmodel::form_types.bootstrap-vue', ['components'=> $form->vue_components, 'type' => $form->options['method']])

                    </div>
                </div>
            </div>
        </div>
		<script>
//			jQuery('#jurisdiction_id').change(function(){ vm.$set('select', jQuery('#jurisdiction_id').val()); console.log('changed');});
//			var jursdiction = document.querySelector('#jurisdiction_id');
//			jursdiction.onchange = function(){
//				vm.$data.data.jurisdiction_id = jursdiction.options[jursdiction.selectedIndex].value;
//			}
		</script>
    </div>
@endsection