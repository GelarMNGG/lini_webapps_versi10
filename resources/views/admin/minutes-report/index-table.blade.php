@extends('layouts.dashboard-datatables')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Daftar aktivitas team';
    $formRouteIndex = 'admin-minutes-report.index';
    $formRouteEdit = 'admin-minutes-report.edit';
    $formRouteCreate = 'admin-minutes-report.create';
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
        <div class='badge badge-info float-left'>{{ count($userMinutes) }}</div>
        <a href="{{ route ($formRouteIndex,'skin='.$skin)}}" class='badge badge-danger float-right'>Change skin</a>
        <strong>{{ ucfirst($pageTitle) }}</strong>
    </div>

    @if (isset($userMinutes))
    <div class="card-body bg-gray-lini-2">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="table-responsive">
                        <table id="" class="display table table-bordered table-bordered dt-responsive nowrap">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama</th>
                                    @if(Auth::user()->company_id == 2)
                                        <th>Departemen</th>
                                    @endif
                                    <th>Kegiatan</th>
                                    <th>Kategori</th>
                                    <th>Tanggal</th>
                                    <th>Bobot</th>
                                    <th>Status</th>
                                    <th>Tanggal selesai</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; ?>
                                @foreach ($userMinutes as $data)
                                    <?php 
                                        if($data->status == 1){
                                            $css = 'success';
                                            $cssTitle = '';
                                        }else{
                                            $cssTitle = 'text-danger';
                                            $css = 'danger';
                                        }
                                    ?>
                                    <tr>
                                        <td>{{ $i }}</td>
                                        <td>{{ ucwords($data->firstname).' '.ucwords($data->lastname) }}</td>
                                        @if(Auth::user()->company_id == 2)
                                            <td><span class="text-info text-uppercase">{{ $data->department_name }}</span></td>
                                        @endif
                                        <td><span class="{{ $cssTitle }}"><strong>{{ ucwords($data->name) }}</strong></span></td>
                                        <td>{{ ucwords($data->category_name) }}</td>
                                        <td><span class="text-{{ $css }}">{{ isset($data->date) ? date('l, d F Y', strtotime($data->date)) : '-'}}</span></td>
                                        <td><strong>{{ $data->grade }}%</strong></td>
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

                                            <a href="{{ route($formRouteEdit, $data->id.'&skin='.$skinBack) }}" class='btn btn-icon waves-effect waves-light btn-info t-white'> <i class='fas fa-edit' title='Edit'></i></a>
                                        </td>
                                    </tr>
                                    <!-- Modal -->
                                    <div class="modal fade" id="minutesModal{{ $data->id }}" tabindex="-1" role="dialog" aria-labelledby="projectMinutes" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered justify-content-center" role="document">
                                            <div class="modal-content-img">
                                                <div class="modal-body text-center">
                                                <button type="button" class="close close-img" data-dismiss="modal" aria-label="Close">
                                                    <img name="image" class="img-fluid" style="margin-bottom:-2px;" src="{{ asset('/img/minutes/user/'.$data->image) }}"  />
                                                    <div class="alert alert-warning" id="projectMinutes">
                                                        <h5>
                                                            Aktifitas: <span class="text-muted">{{ ucfirst($data->name) }}</span>
                                                            <br><span class="text-info"><small>Staff: {{ ucwords($data->firstname).' '.ucwords($data->lastname) }}</small></span>
                                                            <span class="text-info"><small> | @if(isset($data->date)) {{ date('l, d F Y', strtotime($data->date))}} @endif</small></span>
                                                        </h5>
                                                        <small>Keterangan:</small>
                                                        <span class="text-muted"><small>{!! ucfirst($data->description) !!}</small></span>
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
            <div class="row alert alert-warning mt-2">
                <div class="col-md-3">
                    <span><small>DA : Daily Activity</small></span>
                </div>
                <div class="col-md-3">
                    <span><small>TA : Task</small></span>
                </div>
            </div>
        </div> <!-- container-fluid -->
    </div>
    <div class="card-body">
        <div class="col-md">
            <a href="{{ route($formRouteCreate) }}" class="btn btn-orange" type="button"><i class="fa fa-plus"></i> Create daily report</a>
        </div>
    </div>
    @else
    <div class="card-body bg-gray-lini-2">
        <div class="alert alert-warning">Belum ada data.</div>
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
