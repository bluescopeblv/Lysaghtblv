<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TheLoai;
use App\LoaiTin;

class LoaiTinController extends Controller
{
    public function getDanhSach()
    {
    	$loaitin = LoaiTin::all();
    	return view('admin.loaitin.danhsach',['loaitin'=>$loaitin]);
    }

    public function getSua($id)
    {
        $theloai = TheLoai::all();
    	$loaitin = LoaiTin::find($id);
    	return view('admin.loaitin.sua',['loaitin'=>$loaitin,'theloai'=>$theloai]);
    }
    public function postSua(Request $request,$id)
    {
    	$this->validate($request,[
            'Ten'=>'required|unique:LoaiTin,Ten|min:3|max:100',
            'TheLoai'=>'required'
        ],
        [
            'Ten.required'=>'Bạn chưa nhập tên loại tin',
            'Ten.unique'=>'Loại tin đã tồn tại',
            'Ten.min'=>'Tên thể loại từ 3 đến 100 kí tự',
            'Ten.max'=>'Tên thể loại từ 3 đến 100 kí tự',
            'TheLoai.required'=>'Bạn chưa điền tên thể loại'
        ]);
        $loaitin = LoaiTin::find($id);
        $loaitin->Ten = $request->Ten;
        $loaitin->TenKhongDau = changeTitle($request->Ten);
        $loaitin->idTheLoai = $request->TheLoai;
        $loaitin->save();

        return redirect('admin/loaitin/sua/'.$id)->with('thongbao','Bạn đã sửa thành công');
    }
    //-----------------------------------------
    public function getXoa($id)
    {
    	$loaitin = LoaiTin::find($id);
    	$loaitin->delete();
    	return redirect('admin/loaitin/danhsach')->with('thongbao','Bạn đã xóa thành công');
    }
	//-----------------------------------------
    public function getThem()
    {   
        $theloai = TheLoai::all();
    	return view('admin.loaitin.them',['theloai'=>$theloai]);
    }

    public function postThem(Request $request)
    {
    	$this->validate($request,[
    		'Ten'=>'required|unique:LoaiTin,Ten|min:3|max:100',
            'TheLoai'=>'required'
    	],
    	[
    		'Ten.required'=>'Bạn chưa nhập tên',
            'Ten.unique'=>'Loại tin đã tồn tại',
			'Ten.min'=>'Tên thể loại từ 3 đến 100 kí tự',
			'Ten.max'=>'Tên thể loại từ 3 đến 100 kí tự',
            'TheLoai.required'=>'Bạn chưa điền tên thể loại'
    	]);
    	$loaitin = new LoaiTin;
    	$loaitin->Ten = $request->Ten;
    	$loaitin->TenKhongDau = changeTitle($request->Ten);
    	$loaitin->idTheLoai = $request->TheLoai;
        $loaitin->save();
    	//echo  changeTitle($request->Ten);

    	return redirect('admin/loaitin/them')->with('thongbao','Thêm thành công');
    }
}
