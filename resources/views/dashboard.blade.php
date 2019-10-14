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
                      <div class="h5 mb-0 font-weight-bold text-gray-800">$40,000</div>
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
			var chart = new CanvasJS.Chart("new-customer-chart", {
				title:{
					text: "New customers in 12 months"              
				},
				data: [              
				{
					type: "column",
					dataPoints: [
						{ label: "Jan",  y: 124  },
						{ label: "Feb",  y: 10  },
						{ label: "Mar",  y: 10  },
						{ label: "Apr",  y: 10  },
						{ label: "May",  y: 10  },
						{ label: "Jun",  y: 10  },
						{ label: "Jul",  y: 10  },
						{ label: "Aug",  y: 10  },
						{ label: "Sep", y: 15  },
						{ label: "Oct", y: 25  },
						{ label: "Nov",  y: 30  },
						{ label: "Dec",  y: 28  }
					]
				}
				]
			});
			chart.render();

			var chart = new CanvasJS.Chart("top-10-services-chart", {
				title:{
					text: "Top 10 most popular services"              
				},
				data: [              
				{
					type: "column",
					dataPoints: [
						{ label: "Jan",  y: 124  },
						{ label: "Feb",  y: 10  },
						{ label: "Mar",  y: 10  },
						{ label: "Apr",  y: 10  },
						{ label: "May",  y: 10  },
						{ label: "Jun",  y: 10  },
						{ label: "Jul",  y: 10  },
						{ label: "Aug",  y: 10  },
						{ label: "Sep", y: 15  },
						{ label: "Oct", y: 25  },
					]
				}
				]
			});
			chart.render();


		}
</script>
@endpush