
@foreach($drivers as $driver)
	<option value = "{{$driver->id}}" > {{$driver->name}}  {{"(".$driver->email.")"}} </option>
@endforeach