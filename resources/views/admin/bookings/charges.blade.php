<form action = "{{ route('add.booking.charges') }}" method = "POST" id = "booking-charges-form">
    <input type = "hidden" name = "booking_id" value = "{{ $booking->id }}">  
    @csrf
    <div class="row">
            <div class="col-md-6" >
                <label class="form-label">Qouted Amount</label>
                <input type = "number" name = "qouted_amount" class = "form-control-plaintext" value = "{{ $booking->qouted_amount ?? 0 }}" readonly>
            </div>

            <div class="col-md-6" >
                <label class="form-label">Commission %</label>
                <input type = "number" name = "commission" class = "form-control-plaintext" value = "{{ $booking->comission_amount ?? 0 }}" >
            </div>
    </div>

    <div class="row">
            <div class="col-md-6" >
                <label class="form-label">Shipping Charges</label>
                <input type = "number" name = "shipping_charges" class = "form-control-plaintext" value = "{{ $booking->shipping_charges ?? 0 }}" >
            </div>

            <div class="col-md-6" >
                <label class="form-label">Cost of Truck</label>
                <input type = "number" name = "cost_of_truck" class = "form-control-plaintext" value = "{{ $booking->cost_of_truck ?? 0 }}" >
            </div>
    </div>

    <div class="row">
            <div class="col-md-6" >
                <label class="form-label">Border Charges</label>
                <input type = "number" name = "border_charges" class = "form-control-plaintext" value = "{{ $booking->border_charges ?? 0 }}" >
            </div>

            <div class="col-md-6" >
                <label class="form-label">Custom Charges</label>
                <input type = "number" name = "custom_charges" class = "form-control-plaintext" value = "{{ $booking->custom_charges ?? 0 }}" >
            </div>
    </div>

    <div class="row">
            <div class="col-md-6" >
                <label class="form-label">Waiting Charges</label>
                <input type = "number" name = "waiting_charges" class = "form-control-plaintext" value = "{{ $booking->waiting_charges ?? 0 }}" >
            </div>
    </div>
    <hr />
    <h5>For Additional Charges Mention Their Name and Amount.</h5>
    
    @if(count($booking->booking_charges) > 0)
        @foreach($booking->booking_charges as $charge)
        <div class="row">
            <div class="col-md-4" >
                <label class="form-label">Charges Name</label>
                <input type = "text" name = "old_charges_name[{{$charge->id}}]" class = "form-control-plaintext" value = "{{$charge->charges_name ?? ''}}">
            </div>

            <div class="col-md-4" >
                <label class="form-label">Amount</label>
                <input type = "text" name = "old_amount[{{$charge->id}}]" class = "form-control-plaintext" value = "{{$charge->charges_amount ?? 0}}">
            </div>
            <div class="col-md-4" >
                <button type = "button" class = "btn btn-primary btn-xs mt-4 delete-charge" data-id = "{{$charge->id}}"  ><i class = "fa fa-trash"></i></button>
            </div>
        </div>
        @endforeach
    @endif
    <span id = "add-fields">
        <div class="row">
            <div class="col-md-4" >
                <label class="form-label">Charges Name</label>
                <input type = "text" name = "charges_name[]" class = "form-control-plaintext" values = "">
            </div>

            <div class="col-md-4" >
                <label class="form-label">Amount</label>
                <input type = "text" name = "amount[]" class = "form-control-plaintext" values = "">
            </div>
            <div class="col-md-4" >
                <button type = "button" class = "btn btn-primary btn-xs mt-4 add"><i class = "fa fa-plus"></i></button>
            </div>
        </div>
    </span>
    
    <div class="row">
        <hr />
            <div class="col-md-4" >
                <label></label>
                <input type = "text"  class = "form-control-plaintext" value = "Total Amount" readonly>
            </div>

            <div class="col-md-4" >
                <label></label>
                <input type = "text"  class = "form-control-plaintext" value = "{{$booking->total_amount ?? 0}}" readonly>
            </div>
            <div class="col-md-4" >
            </div>
        </div>

    </form>