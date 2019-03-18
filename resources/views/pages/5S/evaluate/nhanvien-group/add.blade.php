@extends('layout.index')

@section('content')
<!-- Page Content -->
    <div id="page-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">5S
                        <small>Thêm Group nhân viên</small>
                    </h1>
                </div>
                <!-- /.col-lg-12 -->
                <div class="col-lg-12" style="padding-bottom:120px">
                    @if(count($errors)>0)
                        <div class="alert alert-danger">
                            @foreach($errors->all() as $err)
                                {{$err}}<br>
                            @endforeach
                        </div>
                    @endif

                    @if(session('thongbao'))
                        <div class="alert alert-success">
                            {{session('thongbao')}}                         
                        </div>
                    @endif

                    <form action="fives/evaluate/nhanvien-group/add" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="_token" value="{{csrf_token()}}">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Tên group nhân viên</label>                                
                                <input name="name" placeholder="Nhập tên group nhân viên" id="name" class="form-control" />

                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Tên group nhân viên</label>
                                <select name="fs_group_id" placeholder="Nhập tên Big group" id="fs_group_id" class="form-control">
                                    <option>Select</option>
                                    @foreach($groups as $key => $val)
                                        <option value="{{$val->id}}">{{$val->name}}</option>
                                    @endforeach
                                </select>                                

                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Note</label>
                                <input name="note" placeholder="Ghi chú" class="form-control" />
                            </div>
                        </div>
                        
                        
                    
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-default">Thêm</button>
                        </div>
                    </form>

                </div>
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </div>
<!-- /#page-wrapper -->

@endsection