@extends('layouts.admin')

@section('content')
    <div class="uk-margin-medium">
        <h1 class="uk-heading-medium">管理員中心</h1>
        <div style="text-align: center; margin-top: 20px;">
            <h3>網站創建以來的總計: {{ $totalOverall }}</h3>
            <h3>今年目前為止的總計: {{ $totalSoFar }}</h3>
        </div>
        <div style="width: 75%; margin: auto;">
            <canvas id="myLineChart"></canvas>
        </div>
    </div>
@endsection

@push("scripts")
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script>
        $(function() {
            var ctx = document.getElementById('myLineChart').getContext('2d');
            var monthlyTotals = @json(array_values($monthlyTotals));
            var myLineChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [{
                        label: '每月總計',
                        data: monthlyTotals,
                        fill: false,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        tension: 0.1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

        });

    </script>
@endpush
