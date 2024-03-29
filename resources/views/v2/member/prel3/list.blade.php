@extends('v2.member.layout.index')
@section('css')
<!-- ===== Plugin CSS ===== -->
<link href="v2/member/plugins/components/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
<!-- <link href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" /> -->
<style type="text/css">
#myTable{
    font-family: "Arial";
    color: black;
    font-size: 12px;
}

myTable.tbody{
    color: black;
    font-size: 10px;
}

.tieude{
    font-family: "Arial";
    color: blue;
    font-size: 20px;
    font-style: bold;
}
</style>
@endsection
@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-info">
            <div class="panel-body">
                <span class="tieude">LOGISTIC - QUẢN LÍ GIAO HÀNG</span>
                <span style="float:right; display: block">
                <a href="delivery2/logistic/add">
                    <button type="button" class="btn btn-warning d-none d-lg-block m-l-15"><i class="fa fa-plus-circle"></i> Tạo kế hoạch mới </button></a></span>
            </div>
        </div>
    </div>
</div>

<!-- /row -->
<div class="row">
    <div class="col-sm-12">
        <div class="white-box">
            <h3 class="box-title m-b-0"></h3>
                <!--  <p class="text-muted m-b-30">Data table example</p>-->            
                <div class="table-responsive">
                <table id="myTable" class="table table-bordered table-striped color-bordered-table info-bordered-table hover-table">
                    <thead>
                        <tr>
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
                            <td>{{ $ttx->khachhang }}</td>
                            <td>{{ $ttx->bienso }}</td>
                            <td>{{ $ttx->giaohangboi }}</td>
                            <td>{{ $ttx->loaihang }}</td>
                            <td>
                                @if($ttx->thoigianlogisticConfirm != NULL)
                                    {{ date('H:i',strtotime($ttx->thoigianlogisticConfirm)) }}
                                @else
                                    <span class="label label-info"><a href="delivery/logistic/confirm/{{$ttx->id}}">OK</a></span>
                                    @if($ttx->status < 30)
                                    <span class="label label-info"><a href="delivery/logistic/wait/{{$ttx->id}}">Chờ</a></span>
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
                                    <span class="label label-info"><a href="delivery/logistic/pay/{{$ttx->id}}">Thanh toán ?</a></span>
                                @endif
                            </td>
                            <td>
                                @if($ttx->thoigianxongDN != NULL)
                                    {{ date('H:i',strtotime($ttx->thoigianxongDN)) }}
                                @else
                                    <span class="label label-info"><a href="delivery/logistic/xongdn/{{$ttx->id}}"> DN ?</a></span>
                                @endif
                            </td>
                            <td>
                                @if($ttx->thoigianxongPXK != NULL)
                                    {{ date('H:i',strtotime($ttx->thoigianxongPXK)) }}
                                @else
                                    <span class="label label-info"><a href="delivery/logistic/xongpxk/{{$ttx->id}}"> PXK ?</a></span>
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
                            </td>
                            
                            <td>
                                <span class="label label-warning">
                                    <a href="delivery2/logistic/edit/{{$ttx->id}}"><span class="glyphicon glyphicon-edit">Sửa</span></a>
                                </span><span>  </span>
                                
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>



@endsection

@section('script')
<script>
$(document).ready(function() {
    $('#myTable').DataTable();
    $(document).ready(function() {
        var table = $('#example').DataTable({
            "columnDefs": [{
                "visible": true,
                "targets": 2
            }],
            "order": [
                [2, 'asc']
            ],
            "displayLength": 25,
            "drawCallback": function(settings) {
                var api = this.api();
                var rows = api.rows({
                    page: 'current'
                }).nodes();
                var last = null;
                api.column(2, {
                    page: 'current'
                }).data().each(function(group, i) {
                    if (last !== group) {
                        $(rows).eq(i).before('<tr class="group"><td colspan="5">' + group + '</td></tr>');
                        last = group;
                    }
                });
            }
        });
        // Order by the grouping
        $('#example tbody').on('click', 'tr.group', function() {
            var currentOrder = table.order()[0];
            if (currentOrder[0] === 2 && currentOrder[1] === 'asc') {
                table.order([2, 'desc']).draw();
            } else {
                table.order([2, 'asc']).draw();
            }
        });
    });
});



</script>
@endsection