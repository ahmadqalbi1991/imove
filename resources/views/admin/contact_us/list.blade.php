@extends("admin.template.layout")

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

              <!-- Ajax Sourced Server-side -->
              <div class="card">
              <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{$mode ?? $page_heading}}</h5>
                  
                </div>
                <div class="card-body">
                  <div class="card-datatable text-nowrap">
                    <div class="table-responsive">
                      <table id="example" class="table table-striped">
                        <thead>
                            <tr>
                                <th class="pt-0" data-colname="country_id">#</th>
                                <th class="pt-0" data-colname="country_name">Name</th>
                                <th class="pt-0" data-colname="country_status">Message</th>
                                <!-- <th class="pt-0" data-colname="iso_code">Message</th>
                                <th class="pt-0" data-colname="dial_code">Dial Code</th> -->
                                
                                <th class="pt-0" data-colname="created_at">Created on</th>
                            </tr>
                          </thead>
                          <tbody>

                          <?php
                    $page = $_GET['page'] ?? 1;
                    $i=0;
                    if(!empty($page) && $page != 1)
                    {
                        $i=($page - 1) * 10;
                    }
                     ?>
                      @foreach($datamain as $value)
                    <?php 
                    
                   
                    $i++ ?>
                          
                          <tr>
                                <td>{{$i}}</td>
                                <td>{{$value->name}}</td>
                                <td>{{$value->message}}</td>
                                <!-- <td>{{$value->dial_code}}</td>
                                <td>{{$value->mobile_number}}</td> -->
                                <td>{{ web_date_in_timezone($value->created_at,'d-M-Y') }}</td>
                            </tr>
                            @endforeach
                          </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
              <!--/ Ajax Sourced Server-side -->






            </div>
@stop
@section('script')
<script src="{{asset('')}}admin-assets/plugins/table/datatable/datatables.js"></script>
<script>
$('#example').DataTable({
      "paging": true,
      "searching": true,
      "ordering": true,
      "info": true,
      "autoWidth": true,
      "responsive": true,
    });</script>

        @endsection
