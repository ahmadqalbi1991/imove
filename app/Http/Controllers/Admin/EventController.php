<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Event;
use App\Traits\StoreImageTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Ozdemir\Datatables\Datatables;
use Ozdemir\Datatables\DB\LaravelAdapter;

class EventController extends Controller
{
    use StoreImageTrait;
    public function index()
    {
        $page_heading = "Events";
        $mode="List";
        return view('admin.events.list', compact('mode','page_heading'));
    }

    public function create($id = '')
    {
        $page_heading = 'Events';
        $mode="Create";
        $event_name = '';
        $status = '';
        $event_date = '';
        $start_time = '';
        $end_time = '';
        $about = '';
        $event_type = '';
        $privacy = '';
        $latitude = '';
        $longitude = '';
        $address = '';
        $image = '';
        $euid = '';
        $permissions = [];

        if ($id) {
            $page_heading = "Edit Event";
            $mode="Edit";
            $id = decrypt($id);
            $event = Event::find($id);
            $event_name = $event->name;
            $status = $event->status;
            $event_date = $event->date;
            $start_time = $event->start_time;
            $end_time = $event->end_time;
            $about = $event->about;
            $event_type = $event->event_type_id;
            $privacy = $event->privacy;
            $latitude = $event->latitude;
            $longitude = $event->longitude;
            $address = $event->address;
            $image = $event->image;
            $euid = $event->euid;

        }

        return view('admin.events.create', compact('mode','page_heading', 'id', 'event_name',
            'event_date', 'event_type', 'privacy','start_time', 'end_time', 'about','status', 'latitude', 'longitude',
            'address', 'image','euid'));

    }

    public function submit(REQUEST $request)
    {
        $o_data = [];
        $errors = [];
        $o_data['redirect'] = route('events');
        $rules = [
            'name' => 'required',
            'date' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            'about' => 'required',
            'event_type_id' => 'required',
            'privacy' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'address' => 'required',
            'status' => 'required',
        ];
        if(!$request->filled('id')){
            $rules['image'] = 'required|image|mimes:jpeg,png,jpg,gif,svg|max:4096';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $status = "0";
            $message = "Validation error occured";
            $errors = $validator->messages();
        } else {
            $event_name = $request->name;
            $event_date = $request->date;
            $start_time = $request->start_time;
            $end_time = $request->end_time;
            $about = $request->about;
            $event_type = $request->event_type_id;
            $privacy = $request->privacy;
            $latitude = $request->latitude;
            $longitude = $request->longitude;
            $address = $request->address;
            $status = $request->status;
            $id = $request->id;
            $owner_id = auth()->user()->id;

            $check = Event::whereRaw('Lower(name) = ?', [strtolower($event_name)])
                ->where('owner_id', '=', $owner_id)
                ->where('id', '!=', $id)->get();
            if ($check->count() > 0) {
                $message = "Event with same name already exists";
                $errors['name'] = 'Event with same name already exists';
            } else {
                if ($id) {
                    DB::beginTransaction();
                    try {

                        $event = Event::find($id);
                        $image = $request->has('image') ? $this->verifyAndStoreImage($request, 'image', 'events')
                            : $event->image;
                        $event->name = $event_name;
                        $event->date = $event_date;
                        $event->start_time = $start_time;
                        $event->end_time = $end_time;
                        $event->about = $about;
                        $event->event_type_id = $event_type;
                        $event->privacy = $privacy;
                        $event->latitude = $latitude;
                        $event->longitude = $longitude;
                        $event->address = $address;
                        $event->active = $status;
                        $event->image = $image;
//                        $event->updated_at = gmdate('Y-m-d H:i:s');
                        $event->save();


                        DB::commit();
                        $status = "1";
                        $message = "Event updated Successfully";

                    } catch (EXCEPTION $e) {
                        DB::rollback();
                        $message = "Faild to update country " . $e->getMessage();
                    }
                } else {
                    DB::beginTransaction();
                    try {
                        $event = new Event();
                        $image = $this->verifyAndStoreImage($request, 'image', 'events');
                        $event->image = $image;
                        $event->name = $event_name;
                        $event->date = $event_date;
                        $event->start_time = $start_time;
                        $event->end_time = $end_time;
                        $event->about = $about;
                        $event->event_type_id = $event_type;
                        $event->privacy = $privacy;
                        $event->latitude = $latitude;
                        $event->longitude = $longitude;
                        $event->address = $address;
                        $event->active = $status;
//                        $event->created_at = gmdate('Y-m-d H:i:s');
//                        $event->updated_at = gmdate('Y-m-d H:i:s');
                        $event->save();
                        $event->euid = $this->generateEuid($event->id);
                        $event->save();
                        DB::commit();
                        $status = "1";
                        $message = "Event Created Successfully";

                    } catch (EXCEPTION $e) {
                        DB::rollback();
                        $message = "Faild to create event " . $e->getMessage();
                    }
                }
            }
        }

        echo json_encode(['status' => $status, 'errors' => $errors, 'message' => $message, 'oData' => $o_data]);
    }

    public function getEventsList(Request $request)
    {

        // $sqlBuilder =  DB::table('variations')

        $sqlBuilder = Event::select([
            'id', 'name', 'date', 'start_time', 'end_time', 'event_type_id', 'privacy', 'active', 'created_at'
        ]);
        $dt = new Datatables(new LaravelAdapter);

        $dt->query($sqlBuilder);


        $dt->edit('created_at', function ($data) {
            return (new Carbon($data['created_at']))->format('d/m/y H:i A');
        });
        $dt->edit('event_type_id', function ($data) {
            return Event::EVENT_TYPES[$data['event_type_id']] ?? '';
        });
        $dt->edit('privacy', function ($data) {
            return Event::PRIVACY[$data['privacy']] ?? '';
        });
        $dt->edit('active', function ($data) {
            $checked = ($data["active"] == 1) ? "checked" : "";
            $html = '<label class="switch s-icons s-outline  s-outline-warning mb-0">
                <input type="checkbox" data-role="active-switch"
                    data-href="' . route('events.status_change', ['id' => encrypt($data['id'])]) . '"
                    ' . $checked . ' >
                <span class="slider round"></span>
            </label>';
            return $html;
        });


        $dt->add('action', function ($data) {
            $html = '<div class="dropdown custom-dropdown">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink7"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                    <i class="flaticon-dot-three"></i>
                </a>

                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink7">';
            if (get_user_permission('events', 'u')) {
                $html .= '<a class="dropdown-item"
                        href="' . route('events.edit', ['id' => encrypt($data['id'])]) . '"><i
                            class="flaticon-pencil-1"></i> Edit</a>';
            }
            if (get_user_permission('events', 'd')) {
                $html .= '<a class="dropdown-item" data-role="unlink"
                        data-message="Do you want to remove this record?"
                        href="' . route('events.delete', ['id' => encrypt($data['id'])]) . '"><i
                            class="flaticon-delete-1"></i> Delete</a>';
            }
            $html .= '</div>
            </div>';
            return $html;
        });

        return $dt->generate();
    }

    public function change_status(REQUEST $request, $id)
    {
        $status = "0";
        $message = "";
        $o_data = [];
        $errors = [];

        $id = decrypt($id);

        $item = Event::where(['id' => $id])->get();
        if ($item->count() > 0) {
            $item = $item->first();
            Event::where('id', '=', $id)->update(['active' => $request->status]);
            $status = "1";
            $message = "Status changed successfully";
        } else {
            $message = "Faild to change status";
        }

        echo json_encode(['status' => $status, 'errors' => $errors, 'message' => $message, 'oData' => $o_data]);

    }

    public function delete(REQUEST $request, $id)
    {
        $status = "0";
        $message = "";


        $id = decrypt($id);

        $category_data = Event::where(['id' => $id])->first();

        if ($category_data) {
            Event::where(['id' => $id])->delete();
            $message = "Event deleted successfully";
            $status = "1";
        } else {
            $message = "Fail to delete record.";
        }

        echo json_encode([
            'status' => $status, 'message' => $message
        ]);
    }

    private function generateEuid($id)
    {
        return "EVT" . str_pad($id.gmdate('dmy'), 8, "0", STR_PAD_LEFT);
    }
}
