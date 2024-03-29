<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\DeliveryThongTinXe;
use App\DeliveryDetail;
use App\DeliveryPicture;
use DB;
use Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;


class Delivery2Controller extends Controller
{
    public function getDanhSach()
    {
    	$thongtinxe = DeliveryThongTinXe::all();
    	return view('pages.delivery.danhsach',compact('thongtinxe'));
    }

    public function getThemThongTin()
    {
    	return view('pages.delivery.themthongtin');
    }

    public function postThemThongTin(Request $request)
    {
    	$this->validate($request,[
            'supplier_name' => 'required',
            'sokien'=>'required',
        ],
        [
            'supplier_name.required'=>'Bạn chưa nhập tên khách hàng',
            'sokien.required'=>'Bạn chưa nhập số kiện'
        ]);

        $thongtinxe = new DeliveryThongTinXe;
        $thongtinxe->supplier_name = $request->supplier_name;
        $thongtinxe->CO = $request->CO;
        $thongtinxe->sokien = $request->sokien;
       
        $thongtinxe->save();
        return redirect()->back()->with('thongbao','Thêm thành công');
    }

    public function import_thongtinxe(Request $request){

        if($request->hasFile('sample_file')){
            $path = $request->file('sample_file')->getRealPath();
            $data = \Excel::load($path)->get();

            //var_dump($data);

            // foreach ($data as $key => $value) {
            // 	echo "key: ".$key. "-  value: ".$value->co;
            // 	echo " <br> ";
            // }
            if($data->count()){
                foreach ($data as $key => $value) {
                    $arr[] = [
                    	'CO' => $value->co,
                        'supplier_name' => $value->supplier_name,
                        'sokien' => $value->sokien,
                        'khoiluong' => $value->khoiluong,
                        'sanpham' => $value->sanpham,
                        'tentaixe' => $value->tentaixe,
                        'bienso' => $value->bienso,
                        'nhaxe' => $value->nhaxe,
                        'soluongxe' => $value->soluongxe,
                        'note' => $value->note,
                        'updated_at' => date('Y-m-d h:m:s'),
                        'created_at' => date('Y-m-d h:m:s'),
                    ];
                }
                if(!empty($arr)){
                    DB::table('delivery_thongtinxe')->insert($arr);
                    //dd('Insert Recorded successfully.');
                    return redirect()->back()->with('thongbao','Import thành công');

                }
            }
        }      
    }

    public function export_thongtinxe($type)
    {
    	$thongtinxe = DeliveryThongTinXe::get()->toArray();
    	//var_dump($thongtinxe);

        return \Excel::create('Sample', function($excel) use ($thongtinxe) {
            $excel->sheet('Sheet 1', function($sheet) use ($thongtinxe)
            {
                $sheet->fromArray($thongtinxe);

            });
        })->download($type);
    }

    public function getXoa($id)
    {
    	$thongtinxe = DeliveryThongTinXe::find($id);
    	$thongtinxe->delete();
    	//echo "Xoas thanh cong".$id."--".$thongtinxe;
    	return redirect()->back()->with('thongbao','Bạn đã xóa thành công');
    }

    public function getSuaThongTin($id)
    {
    	$thongtinxe = DeliveryThongTinXe::find($id);
    	return view('pages.delivery.suathongtin',compact('thongtinxe'));
    }

    public function postSuaThongTin(Request $request,$id)
    {
    	$this->validate($request,[
            'supplier_name' => 'required',
            'sokien'=>'required',
        ],
        [
            'supplier_name.required'=>'Bạn chưa nhập tên khách hàng',
            'sokien.required'=>'Bạn chưa nhập số kiện'
        ]);

        $thongtinxe = DeliveryThongTinXe::find($id);
        $thongtinxe->supplier_name = $request->supplier_name;
        $thongtinxe->CO = $request->CO;
        $thongtinxe->sokien = $request->sokien;
        $thongtinxe->status = $request->status;
       
        $thongtinxe->save();
        return redirect()->back()->with('thongbao','Sửa thành công');
    }

    public function getListBV()
    {
    	$thongtinxe = DeliveryThongTinXe::where('status','<',22)->orWhere('status','>=',80)->get();
    	return view('v2.member.delivery.baove.list',compact('thongtinxe'));
    }

    public function getAddBV()
    {
    	return view('pages.delivery.baove.add');
    }

    public function postAddBV(Request $request)
    {
    	$this->validate($request,[
            'tentaixe' => 'required',
            'bienso'=>'required',
            'taitrongxe' => 'required',
            'chieudaixe'=>'required',
        ],
        [
            'tentaixe.required'=>'Bạn chưa nhập tên tài xế',
            'bienso.required'=>'Bạn chưa nhập biển số xe',
            'taitrongxe.required'=>'Bạn chưa nhập tải trọng xe',
            'chieudaixe.required'=>'Bạn chưa nhập chiều dài xe'
        ]);

        $thongtinxe = new DeliveryThongTinXe;
        $thongtinxe->khachhang = $request->khachhang;
        $thongtinxe->tentaixe = $request->tentaixe;
        $thongtinxe->bienso = $request->bienso;
        $thongtinxe->nhaxe = $request->nhaxe;
        $thongtinxe->taitrongxe = $request->taitrongxe;
        $thongtinxe->chieudaixe = $request->chieudaixe;
        $thoigianxevao = date('Y-m-d H:i:s');
        $thongtinxe->thoigianxevao = $thoigianxevao;   
        $thongtinxe->status = 20;    
        $thongtinxe->save();
        //------------------------------------------------------------------
        //Gửi mail
        $data['title'] = "GIAO HÀNG - THÔNG BÁO XE ĐẾN Ở CỔNG BẢO VỆ SỐ 1";
        $data['name'] = Auth::user()->name;
        $data['sdt'] = Auth::user()->sdt;

        $data['khachhang'] = $request->khachhang;
        $data['tentaixe'] = $request->tentaixe;
        $data['bienso'] = $request->bienso;
        $data['nhaxe'] = $request->nhaxe;
        $data['taitrongxe'] = $request->taitrongxe;
        $data['thoigianxevao'] = $thoigianxevao;

        $subject = 'GIAO HÀNG - THÔNG BÁO XE ĐẾN - KH: '.$request->khachhang;

        Mail::send('emails.delivery.dencong1', $data, function($message) use ($subject) {
            $message->from('l3lysaght.svr01@gmail.com', 'Delivery Project');
            $message->to('phuc.truong@bluescope.com')
                    ->cc('phuc.truong@bluescope.com')
                    ->subject($subject);
            // $message->to('phuc.truong@bluescope.com')
            //         ->subject($subject);
        });
        //-------------------------------------------------------------------

        return redirect()->back()->with('thongbao','Thêm thành công');
    }

    public function getEditBV($id)
    {
    	$thongtinxe = DeliveryThongTinXe::find($id);
    	return view('v2.member.delivery.baove.edit',compact('thongtinxe'));
    }

    public function postEditBV(Request $request,$id)
    {
    	$this->validate($request,[
            'tentaixe' => 'required',
            'bienso'=>'required',
            'taitrongxe' => 'required',
            'chieudaixe'=>'required',
        ],
        [
            'tentaixe.required'=>'Bạn chưa nhập tên tài xế',
            'bienso.required'=>'Bạn chưa nhập biển số xe',
            'taitrongxe.required'=>'Bạn chưa nhập tải trọng xe',
            'chieudaixe.required'=>'Bạn chưa nhập chiều dài xe'
        ]);

        $thongtinxe = DeliveryThongTinXe::find($id);
        $thongtinxe->khachhang = $request->khachhang;
        $thongtinxe->tentaixe = $request->tentaixe;
        $thongtinxe->bienso = $request->bienso;
        $thongtinxe->nhaxe = $request->nhaxe;
        $thongtinxe->taitrongxe = $request->taitrongxe;
        $thongtinxe->chieudaixe = $request->chieudaixe;
        $thongtinxe->thoigianxevao = $request->thoigianxevao;
        $thongtinxe->thoigianxera =  $request->thoigianxera;

        //echo "string".date('Y-m-d H:i:s');
       
        $thongtinxe->save();
        return redirect()->back()->with('thongbao','Sửa thành công');
    }

    public function getDeleteBV($id)
    {
        $thongtinxe = DeliveryThongTinXe::find($id);       
        $thongtinxe->delete();
        return redirect()->back()->with('thongbao','Đã xóa thành công');
    }

    public function getIn_BV($id)
    {
        $thongtinxe = DeliveryThongTinXe::find($id);  
        $thongtinxe->thoigianxevao = date('Y-m-d H:i:s');
        $thongtinxe->status = 20;
        $thongtinxe->save();
                            
        return redirect()->back()->with('thongbao','Đã cập nhật thời gian xe vào thành công');
    }

    public function getOutBV($id)
    {
        $thongtinxe = DeliveryThongTinXe::find($id);  
        $thongtinxe->thoigianxera = date('Y-m-d H:i:s');
        $thongtinxe->status = 90;
        $thongtinxe->save();
                            
        return redirect()->back()->with('thongbao','Đã cập nhật thời gian xe ra thành công');
    }

    public function getListLG()
    {
        $thongtinxe = DeliveryThongTinXe::where('status','>=',10)->get();
        return view('v2.member.delivery.logistic.list',compact('thongtinxe'));
    }

    public function getAdd_LG()
    {
        return view('v2.member.delivery.logistic.add');
    }

    public function postAdd_LG(Request $request)
    {
        $this->validate($request,[
            'khachhang' => 'required',
            
        ],
        [
            'khachhang.required'=>'Bạn chưa nhập tên khách hàng, dự án',
            
        ]);

        $thongtinxe = new DeliveryThongTinXe;
        $thongtinxe->khachhang = $request->khachhang;
        $thongtinxe->tentaixe = $request->tentaixe;
        $thongtinxe->bienso = $request->bienso;
        $thongtinxe->nhaxe = $request->nhaxe;
        $thongtinxe->taitrongxe = $request->taitrongxe;
        $thongtinxe->chieudaixe = $request->chieudaixe;
        $thongtinxe->thoigianxevao = $request->thoigianxevao;
        $thongtinxe->thoigianxera =  $request->thoigianxera;

        $thongtinxe->giaohangboi =  $request->giaohangboi;
        $thongtinxe->loaihang =  $request->loaihang;
        $thongtinxe->thoigianlogisticConfirm =  $request->thoigianlogisticConfirm;
        $thongtinxe->thoigianthanhtoan =  $request->thoigianthanhtoan;
        $thongtinxe->thoigiankehoach =  $request->thoigiankehoach;
        $thongtinxe->thoigiankehoachxera = $request->thoigiankehoachxera;
        $thongtinxe->thoigianxongDN =  $request->thoigianxongDN;
        $thongtinxe->thoigianxongPXK =  $request->thoigianxongPXK;
        $thongtinxe->notelogistic =  $request->notelogistic;

        $thongtinxe->notelogistic =  $request->notelogistic;
        $thongtinxe->maduan =  $request->maduan;
        $thongtinxe->sodonhang =  $request->sodonhang;
        $thongtinxe->tencs =  $request->tencs;
        $thongtinxe->chieudaihang =  $request->chieudaihang;
        $thongtinxe->khoiluonghang =  $request->khoiluonghang;

        if ($request->hasFile('file_pickinglist')) {
            $file = $request->file_pickinglist;

            $fullName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();

            $fullNameLenght = strlen($fullName);
            $extensionLenght = strlen($extension);
            $nameLength = $fullNameLenght - ($extensionLenght + 1);
            $onlyName = substr($fullName, 0, $nameLength);

            $fileNewName = $onlyName.'_'.date('YmdHis').'.'.$file->getClientOriginalExtension();

            $file->move('upload/delivery/pickinglist',$fileNewName);
            $thongtinxe->file_pickinglist = $fileNewName;
        }
        $thongtinxe->status = 10;
        $thongtinxe->save();
        return redirect()->back()->with('thongbao','Đã plan thành công');
    }
    
    public function getEditLG($id)
    {
        $thongtinxe = DeliveryThongTinXe::find($id);
        $pictures = DeliveryPicture::where('thongtinxe_id',$id)->get();

        return view('v2.member.delivery.logistic.edit',compact('thongtinxe','pictures'));
    }

    public function postEditLG($id, Request $request)
    {
        $this->validate($request,[
            'khachhang' => 'required',
           
        ],
        [
            'khachhang.required'=>'Bạn chưa nhập tên khách hàng',
        ]);

        $thongtinxe = DeliveryThongTinXe::find($id);
        $thongtinxe->khachhang = $request->khachhang;
        $thongtinxe->tentaixe = $request->tentaixe;
        $thongtinxe->bienso = $request->bienso;
        $thongtinxe->nhaxe = $request->nhaxe;
        $thongtinxe->taitrongxe = $request->taitrongxe;
        $thongtinxe->chieudaixe = $request->chieudaixe;
        $thongtinxe->thoigianxevao = $request->thoigianxevao;
        $thongtinxe->thoigianxera =  $request->thoigianxera;

        $thongtinxe->giaohangboi =  $request->giaohangboi;
        $thongtinxe->loaihang =  $request->loaihang;
        $thongtinxe->thoigianlogisticConfirm =  $request->thoigianlogisticConfirm;
        $thongtinxe->thoigianthanhtoan =  $request->thoigianthanhtoan;
        $thongtinxe->thoigiankehoach =  $request->thoigiankehoach;
        $thongtinxe->thoigianxongDN =  $request->thoigianxongDN;
        $thongtinxe->thoigianxongPXK =  $request->thoigianxongPXK;
        $thongtinxe->notelogistic =  $request->notelogistic;

        $thongtinxe->thoigianxongDN =  $request->thoigianxongDN;
        
        $thongtinxe->maduan =  $request->maduan;
        $thongtinxe->sodonhang =  $request->sodonhang;
        $thongtinxe->tencs =  $request->tencs;
        $thongtinxe->chieudaihang =  $request->chieudaihang;
        $thongtinxe->khoiluonghang =  $request->khoiluonghang;

        $fileNameCu = $request->file_pickinglist;
        //Kiểm tra file
        if ($request->hasFile('file_pickinglist')) {
            if($fileNameCu <> ""){
                //$pathFileCu = public_path('upload/maintenance/delete/'.$fileNameCu);
                if(file_exists(public_path("upload/delivery/pickinglist/".$fileNameCu))){
                    unlink(public_path("upload/delivery/pickinglist/".$fileNameCu));
                }
            }
            

            $file = $request->file_pickinglist;

            $fullName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();

            $fullNameLenght = strlen($fullName);
            $extensionLenght = strlen($extension);
            $nameLength = $fullNameLenght - ($extensionLenght + 1);
            $onlyName = substr($fullName, 0, $nameLength);

            $fileNewName = $onlyName.'_'.date('YmdHis').'.'.$file->getClientOriginalExtension();

            $file->move('upload/delivery/pickinglist',$fileNewName);
            $thongtinxe->file_pickinglist = $fileNewName;
        }
       
        $thongtinxe->save();
        return redirect()->back()->with('thongbao','Sửa thành công');
    }

    public function getWait_LG($id)
    {
        $thongtinxe = DeliveryThongTinXe::find($id);
        $thongtinxe->status = 30;

        $thongtinxe->save();
        return redirect()->back()->with('thongbao','Cập nhật thành công');

    }

    public function getConfirm_LG($id)
    {
        $thongtinxe = DeliveryThongTinXe::find($id);
        $thongtinxe->thoigianlogisticConfirm = date('Y-m-d H:i:s');
        $thongtinxe->status = 40;
        $thongtinxe->save();
        return redirect()->back()->with('thongbao','Cập nhật thành công');
    }

    public function getPay_LG($id)
    {
        $thongtinxe = DeliveryThongTinXe::find($id);
        $thongtinxe->thoigianthanhtoan = date('Y-m-d H:i:s');
        //$thongtinxe->status = 40;
        $thongtinxe->save();
        return redirect()->back()->with('thongbao','Cập nhật thời gian thanh toán thành công');
    }

    public function getXongDN_LG($id)
    {
        $thongtinxe = DeliveryThongTinXe::find($id);
        $thongtinxe->thoigianxongDN = date('Y-m-d H:i:s');
        $thongtinxe->save();
        return redirect()->back()->with('thongbao','Cập nhật thời gian xong DN thành công');
    }

    public function getXongPXK_LG($id)
    {
        $thongtinxe = DeliveryThongTinXe::find($id);
        $thongtinxe->thoigianxongPXK = date('Y-m-d H:i:s');
        $thongtinxe->save();
        return redirect()->back()->with('thongbao','Cập nhật thời gian xong DN thành công');
    }

    

    public function getListGH()
    {
        $thongtinxe = DeliveryThongTinXe::where('status','>=',30)->get();
        return view('pages.delivery.giaohang.list',compact('thongtinxe'));
    }

    public function getEditGH($id)
    {
        $thongtinxe = DeliveryThongTinXe::find($id);
        $pictures = DeliveryPicture::where('thongtinxe_id',$id)->get();
        return view('pages.delivery.giaohang.edit',compact('thongtinxe','pictures'));
    }

    public function postEditGH($id, Request $request)
    {
        $this->validate($request,[
            'tentaixe' => 'required',
            'bienso'=>'required',
            'taitrongxe' => 'required',
            'chieudaixe'=>'required',
        ],
        [
            'tentaixe.required'=>'Bạn chưa nhập tên tài xế',
            'bienso.required'=>'Bạn chưa nhập biển số xe',
            'taitrongxe.required'=>'Bạn chưa nhập tải trọng xe',
            'chieudaixe.required'=>'Bạn chưa nhập chiều dài xe'
        ]);

        $thongtinxe = DeliveryThongTinXe::find($id);
        $thongtinxe->sanpham = $request->sanpham;
        $thongtinxe->chieudai = $request->chieudai;
        $thongtinxe->khoiluong  = $request->khoiluong;
        $thongtinxe->sokien = $request->sokien;
        $thongtinxe->sodayrang = $request->sodayrang;
        $thongtinxe->noteproduction = $request->noteproduction;
        $thongtinxe->thoigianhuanluyen = $request->thoigianhuanluyen;
        $thongtinxe->thoigianbatdauchathang =  $request->thoigianbatdauchathang;
        $thongtinxe->thoigianketthucchathang =  $request->thoigianketthucchathang;
        $thongtinxe->thoigianbagiaoDN =  $request->thoigianbagiaoDN;
       
        $thongtinxe->save();
        return redirect()->back()->with('thongbao','Sửa thành công');
    }

    public function getDetail_GH($id)
    {
        $thongtinxe = DeliveryThongTinXe::find($id);
        $pictures = DeliveryPicture::where('thongtinxe_id',$id)->get();
        $CO = DeliveryDetail::where('thongtinxe_id',$id)->get();
        return view('pages.delivery.giaohang.detail',compact('thongtinxe','CO','pictures'));
    }

    public function postAddPicture_GH($id, Request $request)
    {
        if ($request->hasFile('link_hinh')) {
            $pictures = new DeliveryPicture;
            $thongtinxe = DeliveryThongTinXe::find($id);
            $pictures->thongtinxe_id = $id;


            $file = $request->link_hinh;

            $fullName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();

            $fullNameLenght = strlen($fullName);
            $extensionLenght = strlen($extension);
            $nameLength = $fullNameLenght - ($extensionLenght + 1);
            $onlyName = substr($fullName, 0, $nameLength);

            $fileNewName = 'ID'.$id.'_'.$onlyName.'_'.date('YmdHis').'.'.$file->getClientOriginalExtension();

            $file->move('upload/delivery/done',$fileNewName);
            $pictures->link_hinh = $fileNewName;

            $pictures->save();
        }

        
        return redirect()->back()->with('thongbao','Thêm ảnh thành công');
    }
    
    public function getDeletePicture_GH($id, $picture_id)
    {

        $picture = DeliveryPicture::find($picture_id);
        $thongtinxe = DeliveryThongTinXe::find($id);
        $file_name = "upload/delivery/done/".$picture->link_hinh;

        $url = getimagesize($file_name);
        if (is_array($url)) {
            unlink($file_name);
            //echo "<br />This file exists!";
        } else {
        }
        $picture->delete();
        return redirect()->back()->with('thongbao','Xóa ảnh thành công');
    }
    

    public function getBatDauChatHang($id)
    {
        $thongtinxe = DeliveryThongTinXe::find($id);
        $thongtinxe->thoigianbatdauchathang = date('Y-m-d H:i:s');
        $thongtinxe->status = 50;
        $thongtinxe->save();
        return redirect()->back()->with('thongbao','Cập nhật thành công');
    }

    public function getKetThucChatHang($id)
    {
        $thongtinxe = DeliveryThongTinXe::find($id);
        $thongtinxe->thoigianketthucchathang = date('Y-m-d H:i:s');
        $thongtinxe->status = 60;
        $thongtinxe->save();
        return redirect()->back()->with('thongbao','Cập nhật thành công');
    }

    public function getHuanLuyenTaiXe($id)
    {
        $thongtinxe = DeliveryThongTinXe::find($id);
        $thongtinxe->thoigianhuanluyen = date('Y-m-d H:i:s');
        //$thongtinxe->status = 60;
        $thongtinxe->save();
        return redirect()->back()->with('thongbao','Cập nhật thành công');
    }

    public function getBanGiaoDN($id)
    {
        $thongtinxe = DeliveryThongTinXe::find($id);
        $thongtinxe->thoigianbagiaoDN = date('Y-m-d H:i:s');
        $thongtinxe->status = 80;
        $thongtinxe->save();
        return redirect()->back()->with('thongbao','Cập nhật thành công');
    }

    public function getDetailCO_LG($id)
    {
        $thongtinxe = DeliveryThongTinXe::find($id);
        $CO = DeliveryDetail::where('thongtinxe_id',$id)->get();
        return view('v2.member.delivery.logistic.detailco.list',compact('thongtinxe','CO'));
    }

    public function getEditCO_LG($id, $CO_id)
    {
        $thongtinxe = DeliveryThongTinXe::find($id);
        $CO = DeliveryDetail::find($CO_id);
        return view('pages.delivery.logistic.detailco.edit',compact('thongtinxe','CO'));
    }

    public function postEditCO_LG($id, $CO_id,Request $request)
    {
        $this->validate($request,[
            'CO' => 'required',
        ],
        [
            'CO.required'=>'Bạn chưa nhập CO',
        ]);

        $CO = DeliveryDetail::find($CO_id);
        $CO->CO = $request->CO;
        $CO->sanpham = $request->sanpham;
        $CO->sodonhang = $request->sodonhang;
        $CO->chitietgiaohang = $request->chitietgiaohang;
        $CO->thongtinxe_id = $id;
        $CO->save();
        
        return redirect()->back()->with('thongbao','Sửa thành công');
    }

    public function getAddCO_LG($id)
    {
        $thongtinxe = DeliveryThongTinXe::find($id);
        return view('pages.delivery.logistic.detailco.add',compact('thongtinxe'));
    }

    public function postAddCO_LG($id, Request $request)
    {
        $this->validate($request,[
            'CO' => 'required',
        ],
        [
            'CO.required'=>'Bạn chưa nhập CO',
        ]);

        $CO = new DeliveryDetail;
        $CO->CO = $request->CO;
        $CO->sanpham = $request->sanpham;
        $CO->sodonhang = $request->sodonhang;
        $CO->chitietgiaohang = $request->chitietgiaohang;
        $CO->thongtinxe_id = $id;
        $CO->save();
        
        return redirect()->back()->with('thongbao','Thêm thành công');
    }
    
    public function import_CO(Request $request, $id){

        if($request->hasFile('sample_file')){
            $path = $request->file('sample_file')->getRealPath();
            $data = \Excel::load($path)->get();

            //var_dump($data);

            if($data->count()){
                foreach ($data as $key => $value) {
                    $arr[] = [
                        'CO' => $value->co,
                        'chitietgiaohang' => $value->chitietgiaohang,
                        'thongtinxe_id' => $id,
                        ];
                }
                if(!empty($arr)){
                    DB::table('delivery_detail')->insert($arr);
                    //dd('Insert Recorded successfully.');
                    return redirect()->back()->with('thongbao','Import thành công');
                }
            }
        }      
    }

    public function export_CO($type)
    {
        $CO = DeliveryDetail::select('CO', 'chitietgiaohang')->take(2)->get()->toArray();
        //var_dump($thongtinxe);

        return \Excel::create('Sample', function($excel) use ($CO) {
            $excel->sheet('Sheet 1', function($sheet) use ($CO)
            {
                $sheet->fromArray($CO);

            });
        })->download($type);
    }

    public function getList_WH()
    {
        $thongtinxe = DeliveryThongTinXe::where('status','>=',30)->where('status','<',80)->get();
        return view('pages.delivery.warehouse.list',compact('thongtinxe'));
    }

    public function getList_IF()
    {
        $thongtinxe = DeliveryThongTinXe::where('status','>=',10)->get();
        return view('pages.delivery.interface.interface1',compact('thongtinxe'));
    }

    public function getInterface_Office_IF()
    {
        $thongtinxe = DeliveryThongTinXe::where('status','>=',10)->get();
        return view('pages.delivery.interface.office',compact('thongtinxe'));
    }


//==========================================================================
//          GUEST
//==========================================================================
    public function getList_RP()
    {
        // $thongtinxe = DeliveryThongTinXe::where('status','>=',10)
        //                 ->where('status','<=',80)
        //                 ->where('status','!=',70)
        //                 ->orwhere('thoigianxera', '>=', date('Y-m-d').' 00:00:00')
        //                 ->get();
        //dd($thongtinxe);
        $today = Carbon::now();
        $ngay =  Carbon::create(Carbon::now()->year, Carbon::now()->month, Carbon::now()->day, 0, 0, 0);
        $ngay2 = Carbon::create(Carbon::now()->year, Carbon::now()->month, Carbon::now()->day, 23, 59, 59);
        //dd($ngay);
        $thongtinxe = DeliveryThongTinXe::where('status','>=',10)
                    ->where('thoigiankehoach','>=',"$ngay")
                    ->where('thoigiankehoach','<=',"$ngay2")
                    ->orderBy('thoigiankehoach')
                    ->get();
        return view('v2.member.delivery.report.list',compact('thongtinxe', 'today','ngay','ngay2'));
    }
    
    public function postList_RP(Request $request)
    {
        $ngay = $request->DateFind;
        $ngay2 = $request->DateFind2;
        $ngay =  Carbon::create(substr($ngay, 0, 4), substr($ngay, 5, 2), substr($ngay, 8, 2), 0, 0, 0);
        $ngay2 = Carbon::create(substr($ngay2, 0, 4), substr($ngay2, 5, 2), substr($ngay2, 8, 2), 23, 59, 59);
        $thongtinxe = DeliveryThongTinXe::where('status','>=',10)
                    ->where('thoigiankehoach','>=',"$ngay")
                    ->where('thoigiankehoach','<=',"$ngay2")
                    ->orderBy('thoigiankehoach')
                    ->get();
        $today = Carbon::now();
        return view('v2.member.delivery.report.list',compact('thongtinxe','today','ngay','ngay2'));
    }

    public function getExport_RP()
    {
        $today = Carbon::now();
        $ngay =  Carbon::create(Carbon::now()->year, Carbon::now()->month, Carbon::now()->day, 0, 0, 0);
        $ngay2 = Carbon::create(Carbon::now()->year, Carbon::now()->month, Carbon::now()->day, 23, 59, 59);
        //dd($ngay);
        $thongtinxe = DeliveryThongTinXe::where('status','>=',10)
                    ->where('thoigiankehoach','>=',"$ngay")
                    ->where('thoigiankehoach','<=',"$ngay2")
                    ->where('status','!=', 70)
                    ->orderBy('thoigiankehoach')
                    ->get();
        return view('v2.member.delivery.report.export',compact('thongtinxe', 'today','ngay','ngay2'));
    }
    
    public function postExport_RP(Request $request){
        $type = "xlsx";
        $today = Carbon::now();
        $ngay = $request->DateFind;
        $ngay2 = $request->DateFind2;
        $ngay =  Carbon::create(substr($ngay, 0, 4), substr($ngay, 5, 2), substr($ngay, 8, 2), 0, 0, 0);
        $ngay2 = Carbon::create(substr($ngay2, 0, 4), substr($ngay2, 5, 2), substr($ngay2, 8, 2), 23, 59, 59);
        

        $thongtinxe = DeliveryThongTinXe::where('status','>=',10)
                    ->where('thoigiankehoach','>=',"$ngay")
                    ->where('thoigiankehoach','<=',"$ngay2")
                    ->where('status','!=', 70)
                    ->orderBy('thoigiankehoach')
                    ->get();

        $duoi1 = date('Ymd');
        $duoi2 = date('His');

        \Excel::create('PREL3 - Delivery - '.$duoi1.' - '.$duoi2, function($excel) use($thongtinxe, $today, $ngay, $ngay2){

            $excel->sheet('New sheet', function($sheet) use($thongtinxe, $today,$ngay, $ngay2){

                $sheet->loadView('v2.member.delivery.report.excel')
                      ->with('thongtinxe', $thongtinxe,
                             'today', $today,
                             'ngay', $ngay,
                             'ngay2', $ngay2);
                      //dd($today);
                      // Format a range with e.g. leading zeros
                // $sheet->setColumnFormat(array(
                //     'AA2:AL500' => 'h:mm:ss'
                // ));
                        // Set background color for a specific cell
                $sheet->getStyle('AG2:AL500')->applyFromArray(array(
                    'fill' => array(
                        'color' => array('rgb' => '00ff00')
                    )
                ));

            });
        })->download($type);

        // \Excel::create('PREL3 - Delivery - '.$duoi1.' - '.$duoi2, function($excel) use ($data) {
        //     $excel->sheet('Sheet 1', function($sheet) use ($data)
        //     {
        //         $sheet->fromArray($data, null, 'A1', false, false);
        //     });
        // })->download($type);

        return redirect()->back();
    } 


    
}
