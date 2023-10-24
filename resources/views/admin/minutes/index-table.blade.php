@extends('layouts.dashboard-datatables')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Daftar aktivitas harian'; 
    $statusBadge    = array('','success','info','danger','purple','pink','warning','dark');
    $formRouteIndex = 'admin-minutes.index';
    $formRouteCreate = 'admin-minutes.create';
    $formRouteStore = 'admin-minutes.store';
    $formRouteEdit = 'admin-minutes.edit';
    $formRouteDestroy = 'admin-minutes.destroy';

    //custom report
    $formRouteCustomReport = 'admin-minutes.customreport';
?>
@endsection

@section('content')
<div class="flash-message mt-2">
    @foreach (['danger','warning','success','info'] as $msg)
        @if (Session::has('alert-'.$msg))
            <p class="alert alert-{{ $msg }}">{{ Session::get('alert-'.$msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
        @endif
    @endforeach
    @if ($errors->any())
        <p class="alert alert-danger">
            <small class="form-text">
                <strong>{{ $errors->first() }}</strong>
            </small>
        </p>
    @endif
</div>

<div class="card">
    <div class="card-header text-center text-uppercase bb-orange">
        <div class='badge badge-info float-left'>{{ count($adminMinutes) }}</div>
        @if($date != NULL)
            <a href="{{ route ($formRouteIndex,'skin='.$skin.'&date='.$date)}}" class='badge badge-danger float-right'>Change skin</a>
        @else
            <a href="{{ route ($formRouteIndex,'skin='.$skin)}}" class='badge badge-danger float-right'>Change skin</a>
        @endif
        <strong>{{ ucfirst($pageTitle) }}</strong>
    </div>

    @if (isset($adminMinutes))
        <div class="card-body bg-gray-lini-2">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table id="" class="display table table-bordered table-bordered dt-responsive nowrap">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        @if(Auth::user()->department_id == 5)
                                            <th>Kategori</th>
                                        @endif
                                        <th>Kegiatan</th>
                                        <th>Tanggal</th>
                                        <th>Status</th>
                                        <th>Tanggal selesai</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 1; ?>
                                    @foreach ($adminMinutes as $data)
                                        <?php 
                                            if($data->status == 1){
                                                $css = 'success';
                                            }else{
                                                $css = 'danger';
                                            }
                                        ?>
                                        <tr>
                                            <td>{{ $i }}</td>
                                            @if(Auth::user()->department_id == 5)
                                                <td><span class="text-danger">{{ isset($data->category_name) ? ucwords($data->category_name) : '-' }}</span></td>
                                            @endif
                                            <td><strong>{{ ucwords($data->name) }}</strong></td>
                                            <td><span class="text-{{ $css }}">{{ isset($data->date) ? date('l, d F Y', strtotime($data->date)) : '-'}}</span></td>
                                            <td>
                                                @if($data->status == 1) 
                                                    <span class="text-info">Done</span> 
                                                @else 
                                                    <span class="text-danger">In progress <small>({{ $data->percentage }}%)</small></span> 
                                                @endif
                                            </td>
                                            <td><span class="text-info">{{ isset($data->done_date) ? date('l, d F Y', strtotime($data->done_date)) : '-'}}</span></td>
                                            <td>
                                                <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#minutesModal{{ $data->id }}"><i class="fas fa-eye"></i> </button>

                                                <form action="{{ route($formRouteDestroy, $data->id) }}" method="POST" style="display:inline;">
                                                    @method('DELETE')
                                                    @csrf

                                                    <a href="{{ route($formRouteEdit, $data->id.'&skin='.$skinBack) }}" class='btn btn-icon waves-effect waves-light btn-info t-white'> <i class='fas fa-edit' title='Edit'></i></a>

                                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin akan menghapus data ini?')"><i class="fas fa-times" title='Delete'></i></button>  
                                                </form>
                                            </td>
                                        </tr>
                                        <!-- Modal -->
                                        <div class="modal fade" id="minutesModal{{ $data->id }}" tabindex="-1" role="dialog" aria-labelledby="projectMinutes" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered justify-content-center" role="document">
                                                <div class="modal-content-img">
                                                    <div class="modal-body text-center">
                                                    <button type="button" class="close close-img" data-dismiss="modal" aria-label="Close">
                                                        <div class="w-100" style="background-color:#d3d3d3; border-top-right-radius:5px;border-top-left-radius:5px;">
                                                            <img name="image" class="img-fluid" style="margin-bottom:-2px;" src="{{ asset('/img/minutes/user/'.$data->image) }}"  />
                                                        </div>
                                                        <div class="alert alert-warning" id="projectMinutes">
                                                            <h5>
                                                                Foto aktifitas: <span class="text-muted">{{ ucfirst($data->name) }}</span>
                                                            </h5>
                                                        </div>
                                                    </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php $i++; ?>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div> <!-- container-fluid -->
        </div>
        <div class="card-body">
            <div class="col-md">
                <a href="{{ route($formRouteCreate,'skin='.$skin) }}" class="btn btn-orange mt-1"><i class="fa fa-plus"></i> Tambah Aktifitas</a>

                <button type="button" class="btn btn-orange mt-1" data-toggle="collapse" data-target="#custom_sort" aria-expanded="false" aria-controls="custom_sort"><i class="fas fa-plus"></i> Pilih tanggal</button>

                <a href="{{ route($formRouteIndex) }}" class="btn btn-blue-lini mt-1"><i class="fa fa-redo-alt"></i></a>

                <div class="collapse" id="custom_sort">
                    <form action="{{ route($formRouteCustomReport) }}" method="post" enctype="multipart/form-data">
                        @csrf

                        <!-- hidden -->
                        <input type="hidden" name="skin" value="{{ $skin }}">
                        
                        <div class="row bg-gray-lini-2">
                            <div class="col-md mt-2">
                                <label for="date">Tanggal <small class="c-red">*</small></label>
                            </div>
                            <div class="w-100"></div>
                            <div class="col-md form-group{{ $errors->has('date') ? ' has-error' : '' }}">
                                <input type="date" class="form-control" name="date" value="{{ old('date') }}">
                                @if ($errors->has('date'))
                                    <small class="form-text text-muted">
                                        <strong>{{ $errors->first('date') }}</strong>
                                    </small>
                                @endif
                            </div>
                            <div class="col-md">
                                <div class="form-group">
                                    <label for=""></label>
                                    <input type="submit" class="btn btn-orange" name="submit" value="Pilih">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @else
        <div class="card-body bg-gray-lini-2">
            <div class="alert alert-warning">Belum ada data.</div>
        </div>
        <div class="card-body">
            <div class="col-md">
                <a href="{{ route($formRouteCreate,'skin='.$skinBack) }}" class="btn btn-orange mt-1"><i class="fa fa-plus"></i> Tambah aktivitas</a>
                <a href="{{ route($formRouteIndex) }}" class="btn btn-blue-lini mt-1"><i class="fa fa-redo-alt"></i></a>
            </div>
        </div>
    @endif
</div> <!-- container-fluid -->
@endsection

@section ('script')
<script>
    $(document).ready(function() {
        $('table.display').DataTable();
    } );
</script>
@endsection
