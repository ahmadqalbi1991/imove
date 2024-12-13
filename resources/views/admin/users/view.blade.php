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
                    <div class="col-md-2 col-sm-4 primary-btn    py-4 rounded">
                        <div class="card">
                            <div class="card-body">
                                <div class="text-center">
                                    <img src="{{get_uploaded_image_url($user->user_image, 'user_image_upload_dir')}}"
                                         class="img-fluid rounded-circle"
                                         alt="Responsive image">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-10 col-sm-8 py-4">
                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label">First Name</label>
                                <div class="form-control-plaintext">{{$user->first_name}}</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Last Name</label>
                                <div class="form-control-plaintext">{{$user->last_name}}</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Full Name</label>
                                <div class="form-control-plaintext">{{$user->name ?? $user->first_name." ".$user->last_name}}</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label">Email</label>
                                <div class="form-control-plaintext">{{$user->email}}</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Phone Number</label>
                                <div class="form-control-plaintext">{{$user->dial_code.$user->phone}}</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Nationality</label>
                                <div class="form-control-plaintext">{{$user->nationalCountry ? $user->nationalCountry->country_name : 'Not Specified'}}</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label">Gender</label>
                                <div class="form-control-plaintext">{{$user->getGender()}}</div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Interested In</label>
                                <div class="form-control-plaintext">{{$user->getInterestedIn()}}</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Current Status</label>
                                <div class="form-control-plaintext">{{$user->getMaritalStatus()}}</div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <label class="form-label">Address</label>
                                <div class="form-control-plaintext">{{$user->address ?? 'Not added yet'}}</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Account Status</label>
                                <div class="form-control-plaintext">{{$user->user_status ? 'Active': 'Inactive'}}</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <label class="form-label">About</label>
                                <div class="form-control-plaintext">{{$user->about_me}}</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <label class="form-label">Location</label>
                            @if($user->lattitude && $user->longitude)
                            <x-elements.map-location
                                addressFieldName="address"
                                :lat="$user->lattitude"
                                :lng="$user->longitude"
                                :address="$user->address"
                                :mapOnly="true"
                            />
                                @else
                                <div class="form-control-plaintext text-center py-4">
                                    <img src="{{asset('images/location-marker.png')}}" style="height: 200px" alt="" class="img-fluid">
                                    <h6 class="h6">No location added yet.</h6>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@section('script')
    <script>
        jQuery(document).ready(function () {

            App.initTreeView();

        })
    </script>
@stop
