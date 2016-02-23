@extends('spark::layouts.app')
@section('scripts')
<script>
    var barChartData = {
        labels : ['{{ date("F",strtotime("-2 Months")) }}','{{ date("F",strtotime("-1 month")) }}' , '{{ date("F",strtotime("now")) }}'],
        datasets : [
            {

                fillColor : "rgba(220,10,10,0.5)",
                strokeColor : "rgba(220,10,10,0.8)",
                highlightFill: "rgba(220,10,10,0.75)",
                highlightStroke: "rgba(220,220,220,1)",
                data : [
                    {{
                        Kregel\Dispatch\Models\Ticket::whereRaw('MONTH(created_at) = ?', [date('m', strtotime('-2 months'))], 'and')->get()->count()
                    }},
                    {{
                        Kregel\Dispatch\Models\Ticket::whereRaw('MONTH(created_at) = ?', [date('m', strtotime('-1 month'))], 'and')->get()->count()
                    }},
                    {{
                        Kregel\Dispatch\Models\Ticket::whereRaw('MONTH(created_at) = ?', [date('m', strtotime('now'))], 'and')->get()->count()
                    }}
                ]
            },
            {
                fillColor : "rgba(10,220,10,0.5)",
                strokeColor : "rgba(10,220,10,0.8)",
                highlightFill: "rgba(10,220,10,0.75)",
                highlightStroke: "rgba(10,220,10,1)",
                data : [
                    {{
                        Kregel\Dispatch\Models\Ticket::whereRaw('MONTH(deleted_at) = ?', [date('m', strtotime('-2 months'))], 'and')->get()->count()
                    }},
                    {{
                        Kregel\Dispatch\Models\Ticket::whereRaw('MONTH(deleted_at) = ?', [date('m', strtotime('-1 month'))], 'and')->get()->count()
                    }},
                    {{
                        Kregel\Dispatch\Models\Ticket::whereRaw('MONTH(deleted_at) = ?', [date('m', strtotime('now'))], 'and')->get()->count()
                    }}
                ]
            }
        ]
    };
    window.onload = function(){
        var ctx = document.getElementById("canvas").getContext("2d");
        window.myBar = new Chart(ctx).Bar(barChartData, {
            responsive : true
        });
    }
</script>
@endsection
@section('content')
<div class="container spark-screen">
    <div class="row">
        <div class="col-md-4">
            @include('dispatch::shared.menu')
        </div>
        <div class="col-md-8">
            @include('dispatch::shared.errors')
            <div class="well white z-offset-2">
                <div class="card-image">
                    <div class="center">
                        <div style="padding:10px;overflow:hidden;width:calc(100% - 10px);">
                            <canvas id="canvas" width="400" height="200"></canvas>
                        </div>
                    </div>
                    <span class="card-title">Ticket Creation</span>
                </div>
                <div class="card-content">
                    <span class="card-title activator grey-text text-darken-4">Ticket Creation<i class="material-icons right">more_vert</i></span>
                    <p><a href="#">Explore more</a></p>
                </div>
                <div class="card-reveal">
                    <span class="card-title grey-text text-darken-4">Ticket Creation<i class="material-icons right">close</i></span>
                    <p>These are the total number of tickets created and closed in the past 3 months for this website.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection