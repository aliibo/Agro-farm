@extends('layouts.tem')
@section('style')
    <style>
        #sudest {
            -webkit-transform: scaleY(-1);
            transform: scaleY(-1);
        }

        #sudouest {
            -webkit-transform: scaleY(-1);
            transform: scaleY(-1);
        }

        .myChart {
            width: 1000px;
            height: 200px;
            /* padding: 10px; */
            border-radius: 10px;
        }

        @media only screen and (max-width:700px) {
            .myChart {
                width: 900px;
                height: 450px;
            }
        }

    </style>
@endsection
@section('content')
{{-- myChart --}}
<div class="card mb-3 mb-md-4">
    <div class="scrollme" style="overflow-x:scroll">
        <div class="card">
            <div class="card-header">Historique de temperature</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="chart-container" style="height:50vh; width:300mm">
                            <canvas class="myChart" id="myChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div><br>
{{-- End myChart --}}

{{-- choice --}}
<div class="row">
    <div class="col-12">
        <!-- Card -->
        <div class="card mb-3 mb-md-4">
            <div class="card-header border-bottom p-0">
                <ul id="wallets" class="nav nav-v2 nav-primary nav-justified d-block d-xl-flex w-100" role="tablist">
                    <li class="nav-item border-bottom border-xl-bottom-0">
                        <a class="nav-link d-flex align-items-center py-2 px-3 p-xl-4 active" href="#day24" role="tab"
                            aria-selected="true" data-toggle="tab">
                            <span>aujourd'hui</span>
                            <small class="text-muted ml-auto">(dernier 24 heures)</small>
                        </a>
                    </li>
                    <li class="nav-item border-bottom border-xl-bottom-0 border-xl-left">
                        <a class="nav-link d-flex align-items-center py-2 px-3 p-xl-4" href="#month30" role="tab"
                            aria-selected="false" data-toggle="tab">
                            <span>dernier mois</span>
                            <small class="text-muted ml-auto">(chaque jour)</small>
                        </a>
                    </li>
                </ul>
            </div>
            <div id="walletsContent" class="card-body tab-content">
                <div class="tab-pane fade show active" id="day24" role="tabpanel">
                    @include('components.day_data')
                    <br>
                </div>

                <div class="tab-pane fade" id="month30" role="tabpanel">
                    @include('components.month_data')
                </div>
            </div>  
        </div>
        <!-- End Card -->
    </div>
</div> 
{{-- End choice--}}

{{-- <div class="card mb-3 mb-md-4">
    <div class="scrollme" style="overflow-x:scroll">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="px-3">
                            <ul id="pills2" class="nav nav-pills nav-pills-v2 mb-3" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" href="#pills2-tab1" role="tab" aria-selected="true"
                                       data-toggle="tab">dernier 24H
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#pills2-tab2" role="tab" aria-selected="false"
                                       data-toggle="tab">dernier mois
                                    </a>
                                </li>
                            </ul>
                            <div id="pillsContent2" class="card-body tab-content p-0">
                                <div class="tab-pane fade show active" id="pills2-tab1" role="tabpanel">
                                    @include('components.day_data')
                                </div>
                        
                                <div class="tab-pane fade" id="pills2-tab2" role="tabpanel">
                                    @include('components.month_data')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> --}}


@endsection

@section('scripts')
@parent
{{-- chart js --}}
<script src="{{ asset('js/liveChart.min.js') }}"></script>

<script>
    var ctx = document.getElementById("myChart");
    var myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Temperateur en C°',
                data: [],
                borderWidth: 1,
                borderColor: 'rgb(0, 108, 191)',
            }]
        },
        options: {
            // responsive: false,
            maintainAspectRatio: false,
            scales: {
                xAxes: [],
                yAxes: [{
                    ticks: {
                        beginAtZero: false
                    }
                }]
            }
        }
    });
    var updateChart = function() {
        $.ajax({
            url: "{{ route('chart_temp') }}",
            type: 'GET',
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(data) {
                myChart.data.labels = data.labels;
                myChart.data.datasets[0].data = data.data;
                myChart.update();
            },
            error: function(data) {
                console.log(data);
            }
        });
    }

    updateChart();
    setInterval(() => {
        updateChart();
    }, 60000);
</script>
{{-- endc chart js --}}
@endsection
