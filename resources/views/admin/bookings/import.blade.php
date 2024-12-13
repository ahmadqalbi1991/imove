@extends("admin.template.layout")
@section('header')
    <style>
    .form-control-plaintext {
            padding-left: 7px;
            border: 1px solid #990253;
            border-radius: 10px;
            color: #212529;
            text-align: left;
            margin-bottom: 15px;
        }

        .form-label {
            margin-bottom: 5px;
        } 

        .error{
            color:red;
        }
        .hide{
            display:none;
        }
    }
    </style>
@endsection
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <!-- Ajax Sourced Server-side -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{$mode ?? $page_heading}}</h5>
            </div>
            <div class="card-body py-0">
                <div class="row">
                
                    
                    <div class="col-md-12 col-sm-12 py-4">
                        <form id="admin_form" action = "{{ route('bookings.import.csv') }}" method = "POST" enctype="multipart/form-data">
                                @csrf
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <label class="form-label d-block" >
                                        <h3>
                                            Download the csv file from here.
                                        </h3>
                                    </label>
                                    <a href = "{{ route('bookings.download.csv') }}" title = "Sample csv file" class="primary-btn btn-hover btn-rounded btn-lg mt-4">Download CSV</a>
                                    <hr />    
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label" >
                                        Select CSV file to upload
                                    </label>
                                    <input type = "file" class = "form-control" name = "csv" accept = ".csv" required>
                                </div>
                                <div class="col-md-12">
                                    <span class = "success_records"></span>
                                    <span class = "error"></span>
                                </div>


                                <div class="col-md-12 text-center">
                                    <button type="submit" class="primary-btn btn-hover btn-rounded btn-lg mt-4 upload"> <span class="hide spinner-border spinner-border-sm"></span> Upload CSV </button>
                                </div>
                            </div>
                        </form>
                        </div>
                    
                </div>
            </div>
        </div>
    </div>
@stop
@section('script')
  <script>
        $(document).ready(function(){

            $('[id]').each(function (i) {
              $('[id="' + this.id + '"]').slice(1).remove();
            });

            $('#admin_form').submit(function(e) {
                e.preventDefault();
                $('.error').html('');
                $('.success_records').html('');
                var formData = new FormData(this);
                $.ajax({
                    type:'POST',
                    url: "{{ route('bookings.import.csv')}}",
                    data: formData,
                    cache:false,
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        $('.upload').prop('disabled', true);    
                        $('.spinner-border').removeClass('hide')
                    },
                    complete: function(){
                        $('.spinner-border').addClass('hide')
                        $('.upload').prop('disabled', false);
                    },
                    success: (data) => {
                    this.reset();
                    App.alert(data.message || 'Bookings have been imported successfully', 'Success!','success');
                    $('.success_records').html('Records successfully imported: '+data.success_records)
                    $.each(data.message_error , function(index, val) { 
                          $('.error').append('<p>'+val+'</p>');
                        });
                    },
                    error: function(xhr, status, error) {
                        var err = eval("(" + xhr.responseText + ")");
                        $('.error').html(err.msg);
                        $('.spinner-border').addClass('hide')
                        $('.upload').prop('disabled', false);                        
                    }
                });
            });

        })
    </script>
@stop
