@extends('layout.index')

@section('content')
    <div class="container">
        <div class="row">
            
            @include('layout.menu')

            <?php
            function doimau($str,$tukhoa)
            {
            	return str_replace($tukhoa, "<span style='color:red;'>$tukhoa</span>", $str);
            }
            ?>

            <div class="col-md-9 ">
                <div class="panel panel-default">
                    <div class="panel-heading" style="background-color:#337AB7; color:white;">
                        <h4><b>Tìm kiếm: {{$tukhoa}}</b></h4>
                    </div>

                    @foreach($tintuc as $tt)
                    <div class="row-item row">
                        <div class="col-md-3">

                            <a href="detail.html">
                                <br>
                                <img width="200px" height="200px" class="img-responsive" src="upload/tintuc/{{$tt->Hinh}}" alt="">
                            </a>
                        </div>

                        <div class="col-md-9">
                            <h3>{!!doimau($tt->TieuDe,$tukhoa)!!}a</h3>
                            <p>{!!doimau($tt->TomTat,$tukhoa)!!}</p>
                            <a class="btn btn-primary" href="detail.html">Xem thêm <span class="glyphicon glyphicon-chevron-right"></span></a>
                        </div>
                        <div class="break"></div>
                    </div>
                    @endforeach
                    
                    <!-- Pagination -->
                    <div style="text-align: center;">
                    	{{$tintuc->links()}}
                    </div>
                    <!-- /.row -->

                </div>
            </div> 

        </div>

    </div>

@endsection