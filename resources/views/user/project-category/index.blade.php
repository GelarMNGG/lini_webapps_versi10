@extends('layouts.dashboard-datatables')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Daftar template yang diajukan'; 
    $dashboardLink  = 'user-projects-template.index';
    $formRouteCreate = 'user-projects-category.create';
    //sub category
    $formRouteSubcatCreate = 'user-projects-subcategory.create';
?>
@endsection

@section('content')
<div class="flash-message">
    @foreach (['danger','warning','success','info'] as $msg)
        @if (Session::has('alert-'.$msg))
            <p class="alert alert-{{ $msg }}">{{ Session::get('alert-'.$msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
        @endif
    @endforeach
</div>

<div class="card">
    <div class="card-header text-center">{{ ucfirst($pageTitle) }}</div>

    <div class="card-body">

        @if (isset($projectReportCategorys))
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="table-responsive">
                        <a href="{{ route($formRouteCreate) }}" class="btn btn-info mb-3"><i class="fa fa-plus"></i> Tambah template</a>
                        <a href="javascript:history.go(-1)" class="btn btn-secondary mb-3"> Kembali</a>
                        <table id="" class="display table table-bordered table-bordered dt-responsive nowrap">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama</th>
                                    <th>Status</th>
                                    <th>Hari diajukan</th>
                                    <th>Hari disetujui</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; ?>
                                @foreach ($projectReportCategorys as $data)
                                    <tr>
                                        <td>
                                            {{ $i }}
                                        </td>
                                        <td>
                                            <strong>{{ ucwords($data->name) }}</strong>
                                            @if(sizeof($projectReportSubcategorys) > 0)
                                                <ul>
                                                    @foreach($projectReportSubcategorys as $dataSubcat)
                                                        @if($dataSubcat->cat_id == $data->id)
                                                        <li>
                                                            {{ ucfirst($dataSubcat->name) }} |

                                                            @if ($dataSubcat->status == 1)
                                                                <span class="text-success">Active</span>
                                                            @else
                                                                <span class="text-danger">Pending</span>
                                                            @endif
                                                        </li>
                                                        @endif
                                                    @endforeach
                                                </ul>
                                            @else
                                            <br>
                                            @endif
                                            @if($data->status == 1)
                                                <a href="{{ route($formRouteSubcatCreate, 'cat_id='.$data->id) }}" class="btn btn-info">Tambah sub kategori</a>
                                            @else
                                                <a href="{{ route($formRouteSubcatCreate, 'cat_id='.$data->id) }}" class="btn btn-info disabled">Tambah sub kategori</a>
                                            @endif

                                        </td>
                                        <td>
                                            @if ($data->status == 1)
                                                <span class="text-success">{{ ucwords($data->status_name) }}</span>
                                            @else
                                                <span class="text-danger">{{ ucwords($data->status_name) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="text-info">{{ isset($data->date_submitted) ? date('l, d F Y',strtotime($data->date_submitted)) : '-' }}</span>
                                        </td>
                                        <td>
                                            <span class="text-success">{{ isset($data->date_approved) ? date('l, d F Y',strtotime($data->date_approved)) : '-' }}</span>
                                        </td>
                                    </tr>
                                    <?php $i++; ?>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div> <!-- container-fluid -->
        @else
            <a href="{{ route($formRouteCreate) }}" class="btn btn-info mb-3"><i class="fa fa-plus"></i> Tambah kategori</a>
            <div class="alert alert-warning">Belum ada data.</div>
        @endif
    </div>
</div> <!-- container-fluid -->
@endsection

@section ('script')
<script>
    $(document).ready(function() {
        $('table.display').DataTable();
    } );
</script>
@endsection
