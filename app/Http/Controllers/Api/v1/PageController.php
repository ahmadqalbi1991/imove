<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\ContactUsModel;
use App\Models\ContactUsSetting;
use App\Models\Page;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PageController extends Controller
{
    public function getCMSDetail(Request $request) {
        try {
            $page = Page::where('slug', $request->page_type)->where('status', 1)->first();
            $oData['page'] = !empty($page) ? convertNumbersToStrings($page->toArray()) : [];

            return return_response('1', 200, 'Page detail fetched', [], $oData);
        } catch (\Exception $exception) {
            return return_response('0', 500, 'Something went wrong');
        }
    }

    public function getContactInfo()
    {
        try {
            $page = ContactUsSetting::first();
            $oData['page'] = !empty($page) ? convertNumbersToStrings($page->toArray()) : [];

            return return_response('1', 200, 'Contact detail fetched', [], $oData);
        } catch (\Exception $exception) {
            return return_response('0', 500, 'Something went wrong');
        }
    }

    public function contactUs(Request $request) {
        try {
            $rules = [
                'subject' => 'required',
                'message' => 'required',
                'access_token' => 'required'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return return_response('0', 401, 'Validation errors', $validator->errors());
            }

            $user = User::where(['user_access_token' => $request->access_token])->first();
            if (empty($user)) {
                return return_response('0', 200, 'No user found');
            }

            $contact = new ContactUsModel();
            $contact->subject = $request->subject;
            $contact->message = $request->message;
            $contact->name = $user->name;
            $contact->email = $user->email;
            $contact->dial_code = $user->dial_code;
            $contact->mobile_number = $user->phone;
            $contact->save();

            return return_response('1', 200, 'Your query is received, administration will contact you.');
        } catch (\Exception $exception) {
            return return_response('0', 500, 'Something went wrong');
        }
    }
}
