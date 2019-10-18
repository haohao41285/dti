@extends('layouts.app')
@section('title','DTI - Dashboard')
@section('content-title')
    Dashboard
@endsection
@section('content')
	<div class="">
          <!-- Content Row -->
          <div class="row">

            <!-- Earnings (Monthly) Card Example -->
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Earnings (Monthly)</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800">$40,000?</div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-calendar fa-2x text-gray-300"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Earnings (Monthly) Card Example -->
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Earnings (Annual)</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800">${{number_format($earnings)}}</div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Earnings (Monthly) Card Example -->
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Pending Tasks</div>
                      <div class="row no-gutters align-items-center">
                        <div class="col-auto">
                          <div class="h5 mb-0 font-weight-bold text-gray-800">{{$pendingTasks}}</div>
                        </div>
                      </div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Pending Requests Card Example -->
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total customers nearly expired</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800">{{$nearlyExpired}}</div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Content Row -->

          <div class="row">

            <!-- Area Chart -->
            <div class="col-xl-6 col-lg-6">
              <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  {{-- <h6 class="m-0 font-weight-bold text-primary">Earnings Overview</h6> --}}

                </div>
                <!-- Card Body -->
                <div class="card-body">
                  <div id="new-customer-chart" style="height: 300px; width: 100%;"></div>
                </div>
              </div>
            </div>

            <!-- Pie Chart -->
            <div class="col-xl-6 col-lg-6">
              <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  {{-- <h6 class="m-0 font-weight-bold text-primary">Revenue Sources</h6> --}}
                </div>
                <!-- Card Body -->
                <div class="card-body">
                  	<div id="top-10-services-chart" style="height: 300px; width: 100%;"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
@endsection
@push('scripts')
<script type="text/javascript">
		window.onload = function () {
			var chartCustomer = new CanvasJS.Chart("new-customer-chart", {
				title:{
					text: "New customers in 12 months"
				},
				data: [
				{
					type: "column",
					dataPoints: [
						{ label: "Jan",  y: {{$newCustomer['1'] ?? '0'}}  },
						{ label: "Feb",  y: {{$newCustomer['2'] ?? '0'}}  },
						{ label: "Mar",  y: {{$newCustomer['3'] ?? '0'}}  },
						{ label: "Apr",  y: {{$newCustomer['4'] ?? '0'}}  },
						{ label: "May",  y: {{$newCustomer['5'] ?? '0'}}  },
						{ label: "Jun",  y: {{$newCustomer['6'] ?? '0'}}  },
						{ label: "Jul",  y: {{$newCustomer['7'] ?? '0'}}  },
						{ label: "Aug",  y: {{$newCustomer['8'] ?? '0'}}  },
						{ label: "Sep",  y: {{$newCustomer['9'] ?? '0'}}  },
						{ label: "Oct",  y: {{$newCustomer['10'] ?? '0'}}  },
						{ label: "Nov",  y: {{$newCustomer['11'] ?? '0'}}  },
						{ label: "Dec",  y: {{$newCustomer['12'] ?? '0'}}  }
					]
				}
				]
			});
			chartCustomer.render();

			var chartServices = new CanvasJS.Chart("top-10-services-chart", {
				animationEnabled: true,
				axisX:{
			    gridThickness: 0,
			    tickLength: 0,
			    lineThickness: 0,
			    labelFormatter: function(){
			      return " ";
			    	}
			  	},
				title:{
					text: "Top 10 most popular services in month"
				},
				data: [
				{
					type: "column",
					dataPoints: [
						{ label: "{{$popularServices[0]['nameService'] ?? ' '}}",  y: {{$popularServices[0]['count'] ?? '0'}}  },
						{ label: "{{$popularServices[1]['nameService'] ?? ' '}}",  y: {{$popularServices[1]['count'] ?? '0'}}  },
						{ label: "{{$popularServices[2]['nameService'] ?? ' '}}",  y: {{$popularServices[2]['count'] ?? '0'}}  },
						{ label: "{{$popularServices[3]['nameService'] ?? ' '}}",  y: {{$popularServices[3]['count'] ?? '0'}}  },
						{ label: "{{$popularServices[4]['nameService'] ?? ' '}}",  y: {{$popularServices[4]['count'] ?? '0'}}  },
						{ label: "{{$popularServices[5]['nameService'] ?? ' '}}",  y: {{$popularServices[5]['count'] ?? '0'}}  },
						{ label: "{{$popularServices[6]['nameService'] ?? ' '}}",  y: {{$popularServices[6]['count'] ?? '0'}}  },
						{ label: "{{$popularServices[7]['nameService'] ?? ' '}}",  y: {{$popularServices[7]['count'] ?? '0'}}  },
						{ label: "{{$popularServices[8]['nameService'] ?? ' '}}",  y: {{$popularServices[8]['count'] ?? '0'}}  },
						{ label: "{{$popularServices[9]['nameService'] ?? ' '}}",  y: {{$popularServices[9]['count'] ?? '0'}}  },

					]
				}
				]
			});
			chartServices.render();
			$("a.canvasjs-chart-credit").remove();
		}
</script>
@endpush

