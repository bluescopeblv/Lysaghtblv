<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DS_Safety;
use Carbon\Carbon;

class DS_Safety_Controller extends Controller
{
    //
    public function get_List()
    {
    	$safety = DS_Safety::orderBy('id','DESC')->get();

    	$now = Carbon::now();
    	return view('pages.dashboard.safety.list', compact('safety'));
    }

    public function get_Add()
    {
    	return view('pages.dashboard.safety.add');
    }

    public function post_Add(Request $request)
    {
    	$this->validate($request,[
            'LTI' => 'required',
            'MTI'=>'required',
        ],
        [
            'LTI.required'=>'Bạn chưa nhập LTI',
            'MTI.required'=>'Bạn chưa nhập MTI',
        ]);

        $hr = new DS_Safety;
        $hr->LTI = date('Y-m-d', strtotime($request->LTI));
        $hr->MTI = date('Y-m-d', strtotime($request->MTI));
          
        $hr->save();
        //------------------------------------------------------------------
        //Gửi mail
        // $data['title'] = "GIAO HÀNG - THÔNG BÁO XE ĐẾN Ở CỔNG BẢO VỆ SỐ 1";
        // $data['name'] = Auth::user()->name;
        // $data['sdt'] = Auth::user()->sdt;

        // $data['khachhang'] = $request->khachhang;
        // $data['tentaixe'] = $request->tentaixe;
        // $data['bienso'] = $request->bienso;
        // $data['nhaxe'] = $request->nhaxe;
        // $data['taitrongxe'] = $request->taitrongxe;
        // $data['thoigianxevao'] = $thoigianxevao;

        // $subject = 'GIAO HÀNG - THÔNG BÁO XE ĐẾN - KH: '.$request->khachhang;

        // Mail::send('emails.delivery.dencong1', $data, function($message) use ($subject) {
        //     $message->from('l3lysaght.svr01@gmail.com', 'Delivery Project');
        //     $message->to('phuc.truong@bluescope.com')
        //             ->cc('phuc.truong@bluescope.com')
        //             ->subject($subject);
            // $message->to('phuc.truong@bluescope.com')
            //         ->subject($subject);
        //});
        //-------------------------------------------------------------------

        return redirect()->back()->with('thongbao','Thêm thành công');
    }

    public function get_Edit($id)
    {
    	$safety = DS_Safety::find($id);
    	return view('pages.dashboard.safety.edit',compact('safety'));
    }

    public function post_Edit($id, Request $request)
    {
    	$this->validate($request,[
            'LTI' => 'required',
            'MTI'=>'required',
        ],
        [
            'LTI.required'=>'Bạn chưa nhập LTI',
            'MTI.required'=>'Bạn chưa nhập MTI',
        ]);

        $safety = DS_Safety::find($id);
        $safety->LTI = date('Y-m-d', strtotime($request->LTI));
        $safety->MTI = date('Y-m-d', strtotime($request->MTI));
          
        $safety->save();
        //------------------------------------------------------------------
        //Gửi mail
        // $data['title'] = "GIAO HÀNG - THÔNG BÁO XE ĐẾN Ở CỔNG BẢO VỆ SỐ 1";
        // $data['name'] = Auth::user()->name;
        // $data['sdt'] = Auth::user()->sdt;

        // $data['khachhang'] = $request->khachhang;
        // $data['tentaixe'] = $request->tentaixe;
        // $data['bienso'] = $request->bienso;
        // $data['nhaxe'] = $request->nhaxe;
        // $data['taitrongxe'] = $request->taitrongxe;
        // $data['thoigianxevao'] = $thoigianxevao;

        // $subject = 'GIAO HÀNG - THÔNG BÁO XE ĐẾN - KH: '.$request->khachhang;

        // Mail::send('emails.delivery.dencong1', $data, function($message) use ($subject) {
        //     $message->from('l3lysaght.svr01@gmail.com', 'Delivery Project');
        //     $message->to('phuc.truong@bluescope.com')
        //             ->cc('phuc.truong@bluescope.com')
        //             ->subject($subject);
            // $message->to('phuc.truong@bluescope.com')
            //         ->subject($subject);
        //});
        //-------------------------------------------------------------------

        return redirect()->back()->with('thongbao','Edit successfully');
    }

    public function get_Delete($id)
    {
    	$hr = DS_Safety::find($id);
    	$hr->delete();
    	return redirect()->back()->with('thongbao','Delete successfully');
    }
}
