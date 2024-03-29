@extends('layout.index')

@section('content')
<div class="container">
	<div class="row page-titles">
        <div class="col-md-2 align-self-center">
            <h4 class="text-themecolor">LOGISTIC</h4>
        </div>
        <div class="col-md-10 align-self-center text-right">
            <div class="d-flex justify-content-end align-items-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Hôm nay {{ date('d-M-Y')}}</a></li>
                    
                        <li class="breadcrumb-item active"><a href="delivery">Quay lại</a></li>
                <a href="delivery/logistic/add">
                    <button type="button" class="btn btn-info d-none d-lg-block m-l-15"><i class="fa fa-plus-circle"></i> Thêm mới </button>
                </a>
                <a href="delivery/logistic/kehoach">
                    <button type="button" class="btn btn-info d-none d-lg-block m-l-15"><i class="fa fa-plus-circle"></i> Xem kế hoạch </button>
                </a>

                </ol>
            </div>
        </div>
    </div>
    
    <div class="card-body">
        <h4 class="card-title"></h4>
        @if(session('thongbao'))
            <div class="alert alert-success">
                {{session('thongbao')}}           
            </div>
        @endif
        <div class="table-responsive m-t-40">
            <table id="myTable" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Kế hoạch</th>
                        <th>Dự án</th>
                        <th>Biển số</th>
                        <th>Giao hàng bởi</th>
                        <th>Type</th>
                        <th>Thời gian confirm</th>
                        <th>Thời gian Kế hoạch</th>
                        <th>Thời gian Thanh toán</th>
                        <th>Thời gian xong DN/DO</th>
                        <th>Thời gian PXK</th>
                        <th>Số CO</th>
                        <th>Số ảnh đã chụp</th>
                        <th>Status</th>
                        <th>Hoạt động</th>
                    </tr>
                </thead>
                <tbody>
                	
                	@foreach($thongtinxe as $ttx)
                    <tr>
                        <td>{{date('d-m',strtotime($ttx->thoigiankehoach))}}</td>
                        <td>{{ $ttx->khachhang }}</td>
                        <td>{{ $ttx->bienso }}</td>
                        <td>{{ $ttx->giaohangboi }}</td>
                        <td>{{ $ttx->loaihang }}</td>
                        <td>
                            @if($ttx->thoigianlogisticConfirm != NULL)
                                {{ date('H:i',strtotime($ttx->thoigianlogisticConfirm)) }}
                            @else
                                @if($ttx->status != 70)
                                <span class="label label-info"><a href="delivery/logistic/confirm/{{$ttx->id}}">OK</a></span>
                                    @if($ttx->status < 30)
                                    <span class="label label-info"><a href="delivery/logistic/wait/{{$ttx->id}}">Chờ</a></span>
                                    @endif
                                @endif
                            @endif
                        </td>
                        
                        <td>
                            @if($ttx->thoigiankehoach != NULL)
                                {{ date('H:i',strtotime($ttx->thoigiankehoach)) }}
                            @else
                                
                            @endif
                        </td>
                        <td>
                            @if($ttx->thoigianthanhtoan != NULL)
                                {{ date('H:i',strtotime($ttx->thoigianthanhtoan)) }}
                            @else
                                @if($ttx->status != 70)
                                <span class="label label-info"><a href="delivery/logistic/pay/{{$ttx->id}}">Thanh toán ?</a></span>
                                @endif
                            @endif
                        </td>
                        <td>
                            @if($ttx->thoigianxongDN != NULL)
                                {{ date('H:i',strtotime($ttx->thoigianxongDN)) }}
                            @else
                                @if($ttx->status != 70)
                                <span class="label label-info"><a href="delivery/logistic/xongdn/{{$ttx->id}}">Xong DN ?</a></span>
                                @endif
                            @endif
                        </td>
                        <td>
                            @if($ttx->thoigianxongPXK != NULL)
                                {{ date('H:i',strtotime($ttx->thoigianxongPXK)) }}
                            @else
                                @if($ttx->status != 70)
                                <span class="label label-info"><a href="delivery/logistic/xongpxk/{{$ttx->id}}">Xong PXK ?</a></span>
                                @endif
                            @endif
                        </td>
                        <td> 
                            <a href="delivery/logistic/detailco/{{$ttx->id}}">{{ getSoLuongCO($ttx->id) }}</a> 
                        </td>
                        <td>
                            {{ getDeliverySoAnh($ttx->id) }}
                        </td>
                        <td>
                            {!! getDeliveryStatus($ttx->status) !!} 
                            {!! getDelivery_Public_Display($ttx->public_display) !!}

                        </td>
                        
                        <td>
                            @if($ttx->status != 70)
                        	<span class="label label-warning">
                        		<a href="delivery/logistic/edit/{{$ttx->id}}"><span class="glyphicon glyphicon-edit">Sửa</span></a>
                        	</span>
                        	@endif
                            <span class="label label-warning">
                                <a href="delivery/logistic/view/{{$ttx->id}}"><span class="glyphicon glyphicon-edit">Xem</span></a>
                            </span>
			            </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    
</div>
@endsection

@section('script')
<script>
  	$(document).ready(function() {
	   $(document).ready(function() {
        var table = $('#myTable').DataTable({
             
            "displayLength": 15,
            "lengthMenu": [[15, 25, 50, -1], [15, 25, 50, "All"]],
             
        });
         
    });
	});
</script>
@endsection