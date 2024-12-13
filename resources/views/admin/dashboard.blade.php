@extends('admin.template.layout')
@section('header')
<link href="{{ asset('') }}admin-assets/assets/css/support-chat.css" rel="stylesheet" type="text/css" />
<link href="{{ asset('') }}admin-assets/plugins/maps/vector/jvector/jquery-jvectormap-2.0.3.css" rel="stylesheet"
   type="text/css" />
<link href="{{ asset('') }}admin-assets/plugins/charts/chartist/chartist.css" rel="stylesheet" type="text/css">
<link href="{{ asset('') }}admin-assets/assets/css/default-dashboard/style.css" rel="stylesheet" type="text/css" />

@stop
@section('content')




<style>
   .home-section footer {
   bottom: auto !important;
   }
</style>
   <div class="row">
        <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
            <div class="icon-card overflow-hidden">
                <div class="icon purple">
                    <i class="fa-regular fa-circle-user"></i>
                </div>
                <div class="content">
                    <h6 class="mb-10">Total Customers</h6>
                    <h3 class="text-bold mb-10">{{$customerData}}</h3>
                </div>
                <a href="{{route('customers.list.all')}}" class="link-icon-card overflow-hidden"></a>
            </div>            
            <!-- End Icon Cart -->
        </div>

        <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
            <div class="icon-card overflow-hidden">
                <div class="icon purple">
                    <i class="fa-regular fa-floppy-disk"></i>
                </div>
                <div class="content">
                    <h6 class="mb-10">Drivers</h6>
                    <h3 class="text-bold mb-10">{{$drivers}}</h3>
                </div>
                <a href="{{route('drivers.list')}}" class="link-icon-card overflow-hidden"></a>
            </div>            
            <!-- End Icon Cart -->
        </div>
       
        <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
            <div class="icon-card overflow-hidden">
                <div class="icon primary">
                    <i class="fa-regular fa-floppy-disk"></i>
                </div>
                <div class="content">
                    <h6 class="mb-10">Received Bookings </h6>
                    <h3 class="text-bold mb-10">{{$bookingData}}</h3>
                </div>
                <a href="{{route('bookings.list.new')}}" class="link-icon-card overflow-hidden"></a>
            </div>            
            <!-- End Icon Cart -->
        </div>

        <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
            <div class="icon-card overflow-hidden">
                <div class="icon primary">
                    <i class="fa-regular fa-floppy-disk"></i>
                </div>
                <div class="content">
                    <h6 class="mb-10">Completed Bookings </h6>
                    <h3 class="text-bold mb-10">{{$bookingDatacompleted}}</h3>
                </div>
                <a href="{{route('bookings.list.new',['status'=>8])}}" class="link-icon-card overflow-hidden"></a>
            </div>            
            <!-- End Icon Cart -->
        </div>


        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div id="calendar" style="min-height: 500px;"></div>
                </div>
            </div>
        </div>

        <!-- <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 mb-4">
            <a href="#!">
                <div class="widget-content-area  data-widgets">
                    <div class="widget  t-1-widget">

                        <div class="media">
                            <div class="icon">
                                <i class="bx bx-user-circle"></i>
                            </div>
                            <div class="media-body text-right">
                                <p class="widget-text mb-0">Users</p>
                                <p class="widget-numeric-value">12</p>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 mb-4">
            <a href="#!">
                <div class="widget-content-area  data-widgets">
                    <div class="widget  t-2-widget">

                        <div class="media">
                            <div class="icon">
                                <i class="bx bx-drink"></i>
                            </div>
                            <div class="media-body text-right">
                                <p class="widget-text mb-0">Vendors</p>
                                <p class="widget-numeric-value">11</p>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 mb-4">
            <a href="#!">
                <div class="widget-content-area  data-widgets">
                    <div class="widget  t-3-widget">

                        <div class="media">
                            <div class="icon">
                                <i class="bx bx-location-plus"></i>
                            </div>
                            <div class="media-body text-right">
                                <p class="widget-text mb-0">Stores</p>
                                <p class="widget-numeric-value">9</p>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 mb-4">
            <a href="#!">
                <div class="widget-content-area  data-widgets">
                    <div class="widget  t-4-widget">

                        <div class="media">
                            <div class="icon">
                                <i class="bx bxs-megaphone"></i>
                            </div>
                            <div class="media-body text-right">
                                <p class="widget-text mb-0">Orders</p>
                                <p class="widget-numeric-value">8</p>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div> -->
    </div>

    <div class="row">



       

        

       



       
    </div>
<div class="row mb-4">






   <div class="col-lg-12">
      <div class="row">
         <div class="col-12">
            <div class="row">
               <!-- <div class="col-lg-6">
                  <div class="card custom-card">
                     <b style="color: black;">Order Product Statistics</b>
                     <div class="chart" style="position: relative; height: 35vh; overflow: hidden;">
                        <canvas id="orderschart"></canvas>
                     </div>
                  </div>
               </div> -->
               {{-- 
               <div class="col-lg-6">
                  <div class="card custom-card">
                     <b style="color: black;">Order Product Statistics</b>
                     <div class="chart" style="position: relative; height: 35vh; overflow: hidden;">
                        <canvas id="vendorschart"></canvas>
                     </div>
                  </div>
               </div>
               --}}
            </div>
            <!-- <div class="row mt-4">
               <div class="col-lg-12">
                  <div class="card custom-card">
                     <div class="col-lg-12">
                        <b style="color: black;">Vendor Registration</b>
                        <ul class="nav justify-content-sm-end justify-content-center monthly-chart-tab nav-pills"
                           id="monthly-chart" role="tablist">
                           <li class="nav-item">
                              <a class="nav-link active" id="monthly-chart-weekly-tab" data-toggle="pill"
                                 href="#monthly-chart-weekly" role="tab"
                                 aria-controls="monthly-chart-weekly" aria-selected="true">Last 7 Days</a>
                           </li>
                           <li class="nav-item">
                              <a class="nav-link" id="monthly-chart-monthly-tab" data-toggle="pill"
                                 href="#monthly-chart-monthly" role="tab"
                                 aria-controls="monthly-chart-monthly" aria-selected="true">Monthly</a>
                           </li>
                           <li class="nav-item">
                              <a class="nav-link" id="monthly-chart-yearly-tab" data-toggle="pill"
                                 href="#monthly-chart-yearly" role="tab"
                                 aria-controls="monthly-chart-yearly" aria-selected="false">Yearly</a>
                           </li>
                        </ul>
                     </div>
                     <div class="tab-content" id="monthly-chartContent">
                        <div class="tab-pane fade show active" id="monthly-chart-weekly" role="tabpanel"
                           aria-labelledby="monthly-chart-weekly-tab">
                           <div class="v-pv-weekly" style="height: 300px; width: 100%; margin-top: 30px;">
                           </div>
                        </div>
                        <div class="tab-pane fade" id="monthly-chart-monthly" role="tabpanel"
                           aria-labelledby="monthly-chart-monthly-tab">
                           <div class="v-pv-monthly" style="height: 300px; width: 100%; margin-top: 30px;">
                           </div>
                        
                        </div>
                        <div class="tab-pane fade" id="monthly-chart-yearly" role="tabpanel"
                           aria-labelledby="monthly-chart-yearly-tab">
                           <div class="v-pv-yearly" style="height: 300px; width: 100%; margin-top: 30px;">
                           </div>
                          
                        </div>
                     </div>
                  </div>
               </div>
            </div> -->
            
         </div>
      </div>
   </div>
</div>
</div>
@stop
@section('footer')
<script src="{{asset('')}}admin-assets/plugins/charts/chartist/chartist.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"
   integrity="sha512-ElRFoEQdI5Ht6kZvyzXhYG9NqjtkmlkfYk0wr6wHxU9JEHakS7UJZNeml5ALk+8IKlU6jDgMabC3vkumRokgJA=="
   crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0-rc"></script>
@stop
@section('script')
<script>
//   var orderschartctx = document.getElementById("orderschart");
//   var myChart = new Chart(orderschartctx, {
//       type: 'doughnut',
//       data: {
//           labels: ['Pending', 'Accepted', 'Ready For Delivery', 'Dispatched', 'Delivered', 'Cancelled',
//               'Return Pending', 'Returned', 'Return Rejected'
//           ],
//           datasets: [{
//               label: '',
//               data: [10, 20,
//                   10,
//                   15, 30,
//                   20, 1,
//                   0, 1
//               ],
//               backgroundColor: [
//                   'rgba(202, 153, 67, 0.8)',
//                   'rgba(0, 0, 0, 0.8)',
//                   'rgba(235, 192, 94, 0.8)',
//                   'rgba(73, 0, 0, 0.8)',
//                   'rgba(156, 210, 57, 0.8)',
//                   'rgba(99, 68, 94, 0.8)',
//                   'rgba(189, 100, 24, 0.8)',
//                   'rgba(45, 28, 85, 0.8)',
//                   'rgba(55, 222, 1, 0.8)',
//               ],
//               borderColor: [
//                   'rgba(202, 153, 67, 0.8)',
//                   'rgba(0, 0, 0, 0.8)',
//                   'rgba(235, 192, 94, 0.8)',
//                   'rgba(73, 0, 0, 0.8)',
//                   'rgba(156, 210, 57, 0.8)',
//                   'rgba(99, 68, 94, 0.8)',
//                   'rgba(189, 100, 24, 0.8)',
//                   'rgba(45, 28, 85, 0.8)',
//                   'rgba(55, 222, 1, 0.8)',
//               ],
//               borderWidth: 2
//           }]
//       },
//       options: {
//           cutout: 60,
//           centerPercentage: 80,
//           responsive: true,
//           maintainAspectRatio: false,
//           tooltips: {
//               enabled: true
//           },
//           interaction: {
//               intersect: false
//           },
//           plugins: {
//               legend: {
//                   display: true,
//                   position: 'bottom',
   
//                   labels: {
//                       font: {
//                           size: 10
//                       },
//                       boxWidth: 10
//                   }
//               }
//           },
//       }
//   });
   
   

</script>
<script>
//     var ctx = document.getElementById('myChart').getContext('2d');
//     var chart = new Chart(ctx, {
// 	// The type of chart we want to create
// 	type: 'line', // also try bar or other graph types

// 	// The data for our dataset
// 	data: {
// 		labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "July", "Aug", "Sep", "Oct", "Nov", "Dec"],
// 		// Information about the dataset
//     datasets: [{
// 			label: "Statistics",
// 			// backgroundColor: 'rgb(237 98 47)',
// 			// borderColor: 'rgb(255 150 206)',
//             tension: 0.5,
//             pointBackgroundColor: "rgb(237 98 47)",
//             pointHoverBackgroundColor: "rgb(237 98 47)",
//             pointBorderColor: "transparent",
//             pointHoverBorderColor: "#fff",
//             pointHoverBorderWidth: 3,
//             pointBorderWidth: 5,
//             pointRadius: 5,
//             pointHoverRadius: 8,
// 			data: [26.4, 39.8, 66.8, 66.4, 40.6, 55.2, 77.4, 69.8, 57.8, 76, 110.8, 142.6],
// 		}]
// 	},

// 	// Configuration options
// 	options: {
//         responsive: true,
//         layout: {
//             padding: 10,
//         },
// 		plugins: {
//             legend: {
//             display: false
//             }
//         },
//         scales: {
//             yAxes: {
//                     grid: {
//                         display: false,
//                         drawTicks: false,
//                         drawBorder: false,
//                     },
//                     ticks: {
//                         padding: 35,
//                         max: 300,
//                         min: 50,
//                     },
//             },
//                 xAxes:
//                     {
//                     grid: {
//                         drawBorder: false,
//                         color: "rgba(143, 146, 161, .1)",
//                         zeroLineColor: "rgba(143, 146, 161, .1)",
//                     },
//                     ticks: {
//                         padding: 20,
//                     },
//                     },
//         },
// 	}
// });

</script>
<script>
   var data1 = {
       labels: [],
       series: [
   
   
          '',
       ]
   };
   
   var options = {
       seriesBarDistance: 10,
       axisY: {
           labelInterpolationFnc: function(value) {
               return value + '';
           },
           onlyInteger: true,
       }
   };
   
   var responsiveOptions = [
       ['screen and (max-width: 575px)', {
           seriesBarDistance: 5,
           axisX: {
               labelInterpolationFnc: function(value) {
                   return value[0];
               }
           }
       }]
   ];
   new Chartist.Bar('.v-pv-weekly', data1, options, responsiveOptions);
   $('.monthly-chart-tab li a').on('shown.bs.tab', function(event) {   
       var responsiveOptionsMonthly = [
           ['screen and (max-width: 575px)', {
               axisX: {
                   labelInterpolationFnc: function(value) {
                       return value[0];
                   }
               }
           }]
       ];
   
       new Chartist.Line('.v-pv-monthly', {
           labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov',
               'Dec'],
           series: [
               '',
           ]
       }, {
           fullWidth: true,
           axisY: {
               onlyInteger: true,
               offset: 20,
               labelInterpolationFnc: function(value) {
                   return value + '';
               },
           }
       }, responsiveOptionsMonthly);
   
   
       var responsiveOptionsYearly = [
           ['screen and (max-width: 575px)', {
               axisX: {
                   labelInterpolationFnc: function(value) {
                       return value[2] + value[3];
                   }
               }
           }]
       ];
   
       new Chartist.Line('.v-pv-yearly', {
           labels: [2016, 2017, 2018, 2019, 2020, 2021, 2022],
           series: [
            '',
           ]
       }, {
           low: 0,
           showArea: true,
           axisY: {
               onlyInteger: true,
               offset: 20,
               labelInterpolationFnc: function(value) {
                   return value + '';
               },
           }
       }, responsiveOptionsYearly);
   
   })
   
 
</script>

<script>
// var bookingsData = {!! json_encode($bookings) !!};

// var ctx = document.getElementById('bookingsChart').getContext('2d');
// var chart = new Chart(ctx, {
//     type: 'bar',
//     data: {
//         labels: bookingsData.map(data => data.month),
//         datasets: [{
//             label: 'New Requests',
//             data: bookingsData.map(data => data.count),
//             backgroundColor: 'rgba(54, 162, 235, 0.2)',
//             borderColor: 'rgba(54, 162, 235, 1)',
//             borderWidth: 1
//         }]
//     },
//     options: {
//         scales: {
//             yAxes: [{
//                 ticks: {
//                     beginAtZero: true
//                 }
//             }]
//         }
//     }
// });
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
<script>
// var ctx = document.getElementById('myChartPie').getContext('2d');
// var myChart = new Chart(ctx, {
//     type: 'pie',
//     data: {
//         labels: ['Customers', 'Companies'],
//         datasets: [{
//             data: [{{$customerData}}, {{$driverData}}],
//             backgroundColor: [
//                 'rgba(255, 99, 132, 1)',
//                 'rgba(54, 162, 235, 1)',
//             ],
//             borderColor: [
//                 'rgba(255, 99, 132, 0.2)',
//                 'rgba(54, 162, 235, 0.2)',
//             ],
//             borderWidth: 1
//         }]
//     },
//     options: {
//         responsive: true,
//         maintainAspectRatio: false
//     }
// });
</script>


<script>
// var customersData = {!! json_encode($customersss) !!};

// var ctx = document.getElementById('customerChart').getContext('2d');
// var chart = new Chart(ctx, {
//     type: 'bar',
//     data: {
//         labels: customersData.map(data => data.month),
//         datasets: [{
//             label: 'New Customers',
//             data: customersData.map(data => data.count),
//             backgroundColor: 'rgba(54, 162, 235, 0.2)',
//             borderColor: 'rgba(54, 162, 235, 1)',
//             borderWidth: 1
//         }]
//     },
//     options: {
//         scales: {
//             yAxes: [{
//                 ticks: {
//                     beginAtZero: true
//                 }
//             }]
//         }
//     }
// });


    // function calenderLoad() {
        var calendarEl = document.getElementById("calendar");

        var calendar = new FullCalendar.Calendar(calendarEl, {
            customButtons: {
                myCustomButton: {
                    icon: "bell",
                    click: function () {
                        alert("clicked the custom button!");
                    },
                },
            },
            headerToolbar: {
                left: "prev,today,next",
                // left: 'prevYear,prev,today,next,nextYear myCustomButton',
                center: "title",
                right: "dayGridMonth,timeGridWeek,listMonth",
                // right: 'multiMonthYear,dayGridMonth,timeGridWeek,listMonth'
            },

            eventClick: function (events, jsEvent, view) {
                $("#event-modal").modal("show");
                $("#modal-title").html(events.title);
                $("#modal-start").html(events.start);
                $("#modal-end").html(events.end);
            },

            themeSystem: "bootstrap5",
            // buttonIcons: {
            //   close: 'x-octagon',
            //   prev: 'arrow-left-circle-fill',
            //   next: 'arrow-right-circle-fill',
            //   prevYear: 'arrow-return-left',
            //   nextYear: 'arrow-return-right'
            // },
            initialView: "dayGridMonth", // Set the default view to weekly
            initialDate: "{{date('Y-m-d')}}",
            editable: false,
            // render: true,
            dayMaxEvents: true, // allow "more" link when too many events
            navLinks: true,
            eventLimit: true,
            eventTimeFormat: {
                // like '14:30:00'
                hour: "2-digit",
                minute: "2-digit",
                hour12: true,
            },
            events: [

                // Loop through php $received_deliveries variabe and generate the events
                @foreach($received_deliveries as $delivery)
                {
                    title: "#{{ strtoupper($delivery->order_number) }}",
                    start: "{{ $delivery->date }}",
                    end: "{{ $delivery->date }}",
                    url: "{{ route('admin.bookings.view_request', ['id' => $delivery->id]) }}",
                },
                @endforeach
        
            ],
        });

        calendar.render();
        // });
    // }
</script>
@stop