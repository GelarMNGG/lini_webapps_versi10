@extends('layouts.dashboard-datatables')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Daftar bug report'; 
    $statusBadge    = array('','success','info','danger','purple','pink','warning','dark');
    $formRouteIndex = 'user-bug-report.index';
    $formRouteCreate = 'user-bug-report.create';
    $formRouteStore = 'user-bug-report.store';
    $formRouteEdit = 'user-bug-report.edit';
    $formRouteDestroy = 'user-bug-report.destroy';
?>
@endsection

@section('content')
<div class="flash-message mt-2">
    @foreach (['danger','warning','success','info'] as $msg)
        @if (Session::has('alert-'.$msg))
            <p class="alert alert-{{ $msg }}">{{ Session::get('alert-'.$msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
        @endif
    @endforeach
</div>

    @if (count($dataReports) > 0)
        <div class="card mt-2">
            <div class="card-header text-center text-uppercase bb-orange">
                <div class='badge badge-info float-left'>{{ count($dataReports) }}</div>
                <strong>{{ ucfirst($pageTitle) }}</strong>
            </div>

            <div class="card-body bg-gray-lini-2">
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif
                <div class="row m-0">
                    @foreach($dataReports as $data)
                        <div class="col-6 p-2 small">
                            <div class="bg-card-box br-5 p-2">
                                <div class="img-ca-box">
                                    @if(isset($data->image) && $data->image != 'default.png')

                                        <button type="button" class="btn badge-pill text-dark" data-toggle="modal" style="position:absolute;" data-target="#bugreportsModal{{ $data->id }}"><i class="fas fa-eye"></i> </button>

                                        <!-- modal image view -->
                                            <div class="modal fade" id="bugreportsModal{{ $data->id }}" tabindex="-1" role="dialog" aria-labelledby="projectExpenses" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered justify-content-center" role="document">
                                                    <div class="modal-content-img">
                                                        <div class="modal-body text-center">
                                                        <button type="button" class="close close-img" data-dismiss="modal" aria-label="Close">
                                                            <img name="image" class="img-fluid" style="margin-bottom:-2px;" src="{{ asset('/img/bug-report/user/'.$data->image) }}"  />
                                                            <div class="alert alert-warning mt-1" id="projectExpenses">
                                                                <h5>
                                                                    Bug report: <span class="text-muted">{{ ucfirst($data->name) }}</span>
                                                                </h5>
                                                                <span class="text-info">{{ isset($data->status_name) ? ucwords($data->status_name) : 'Submitted' }}</span>

                                                                @if(isset($data->reproduce))
                                                                <br><span class="text-dark small"><strong>Reproduce : </strong>{{  ucwords($data->reproduce) }}</span>
                                                                @endif

                                                                <br><span class="text-dark small"><strong>Deskripsi : </strong>{{ isset($data->description) ? ucfirst($data->description) : '-' }}</span>
                                                            </div>
                                                        </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <!-- modal image view end -->

                                        <img class="img-ca" src="{{ asset('img/bug-report/user/'.$data->image) }}">
                                    @else
                                        <div class="p-5 text-center"><img class="img-ca-2" src="{{ asset('img/bug-report/default.png') }}"></div>
                                    @endif
                                </div>

                                <br><span class="text-info"><strong>Nama Bug : </strong></span>{{ isset($data->name) ? ucwords($data->name) : 'Belum ada data' }}
                                <br><span class="text-danger"><strong>Error terjadi saat? : </strong></span>{{ isset($data->reproduce) ? ucwords($data->reproduce) : 'Belum ada data' }}
                                <br><span class="text-warning"><strong>Deskripsi : </strong></span>{{ isset($data->description) ? ucfirst($data->description) : 'Belum ada data' }}
                                <br><span class="text-success"><strong>Status : </strong></span>{{ isset($data->status_name) ? ucwords($data->status_name) : 'Submitted' }}
                                
                                <div class="mt-1">
                                    <form action="{{ route($formRouteDestroy, $data->id) }}" method="POST">
                                        @method('DELETE')
                                        @csrf

                                        <a href="{{ route($formRouteEdit, $data->id) }}" class='btn btn-icon waves-effect waves-light btn-info t-white'> <i class='fas fa-edit' title='Edit'></i> Ubah</a>

                                        <!-- IT Department -->
                                        @if(Auth::user()->department_id == 5)
                                            <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin akan menghapus data ini?')"><i class="fas fa-times" title='Delete'></i> Hapus</button>  
                                        @endif
                                        <!-- IT Department end -->
                                    </form>
                                </div>

                            </div>
                        </div>
                    @endforeach
                    <div class="col-12">
                        <?php 
                            #$techMinutes->setPath('minutes-tech?project_id='.$projectTask->project_id.'&task_id='.$projectTask->id);
                            #{{ $dataReports->links() }}
                        ?>
                        <?php $paginator = $dataReports; ?>
                        @include('includes.paginator')
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="col-md">
                    <a href="{{ route($formRouteCreate) }}" class="btn btn-orange"><i class="fa fa-plus"></i> Tambah Bug Report</a>
                </div>
            </div>
        </div> <!-- card -->
    @else
        <div class="card mt-2">
            <div class="card-header text-center text-uppercase bb-orange">
                <div class='badge badge-info float-left'>{{ count($dataReports) }}</div>
                <strong>{{ ucfirst($pageTitle) }}</strong>
            </div>

            <div class="card-body bg-gray-lini-2">
                <div class="alert alert-warning">Belum ada data.</div>
            </div>

            <div class="card-body">
                <div class="col-md">
                    <a href="{{ route($formRouteCreate) }}" class="btn btn-orange"><i class="fa fa-plus"></i> Tambah Bug Report</a>
                </div>
            </div>
        </div> <!-- container-fluid -->
    @endif
@endsection

@section ('script')
<script>
    $(document).ready(function() {
        $('table.display').DataTable();
    } );
</script>
@endsection
