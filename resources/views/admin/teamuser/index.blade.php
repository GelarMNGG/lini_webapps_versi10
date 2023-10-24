@extends('layouts.dashboard-datatables')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Daftar staff';

    $formRouteIndex  = 'teamuser.index';
    $formRouteCreate = 'teamuser.create';
    $formRouteEdit = 'teamuser.edit';
    $formRouteDestroy = 'teamuser.destroy';
    //title
    $formTitleIndex = 'teamusertitle.index';
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
<div class="card mt-2">
    <div class="card-header text-center text-uppercase bb-orange">
        <strong>{{ ucfirst($pageTitle) }}</strong>
        <a href="{{ route ($formRouteIndex,'skin='.$skin)}}" class='badge badge-danger float-right'>Change skin</a>
    </div>

    @if (isset($teamusers) && count($teamusers) > 0)
        <div class="card-body bg-gray-lini-2">
            <div class="row">
                <?php $separator = 1; ?>
                @foreach ($teamusers as $data)
                    <div class="col-sm p-2">
                        <div class="bg-card-box br-5 p-2">
                            
                            @if($data->active == 1)
                                <div class="badge badge-success float-right">Active</div>
                            @else
                                <div class="badge badge-danger float-right">Inactive</div>
                            @endif
                            <div class="float-left mr-1">
                                <img src="{{ asset('admintheme/images/users/'.$data->image) }}" alt="user-image" class="rounded-circle avatar-md">
                            </div>
    
                            <span class="text-info">
                                @if(isset($data->firstname))
                                    {{ ucwords($data->firstname).' '.ucwords($data->lastname) }}
                                @else
                                    {{ ucwords($data->name) }}
                                @endif
                            </span>
    
                            <br>
                            @if(isset($data->role_name))
                                {{ ucfirst($data->role_name) }}
                            @else
                                {{ ucfirst($data->level_name) != null ? ucfirst($data->level_name) : 'Staff' }}
                            @endif

                            <br>
                            <small class="text-danger">{{ strtoupper($data->department_name) }}</small>
    
                            <div>
                                @if ($data->mobile !== null)
                                    <a href='https://wa.me/{{ $data->mobile }}?text=Haloo ' target='_blank' class='btn btn-icon waves-effect waves-light btn-success t-white'> <i class='fab fa-whatsapp' title='Whatsapp'></i></a>
                                @endif
                                
                                <form action="{{ route($formRouteDestroy, $data->id) }}" style="display:inline-block;" method="POST">
                                @method('DELETE')
                                @csrf
                                    <a href="{{ route($formRouteEdit, $data->id) }}" class='btn btn-icon waves-effect waves-light btn-info t-white'> <i class='fas fa-edit' title='Edit'></i></a>
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin akan menghapus data ini?')"><i class="fas fa-times" title='Delete'></i></button>  
                                </form>
                            </div>
                            
                        </div>
                    </div> <!-- container-fluid -->
                    <?php if($separator % 2 == 0){echo "<div class='w-100'></div>";} ?>
                    <?php $separator++; ?>
                @endforeach
                <div class="w-100"></div>
                <div class="col-sm">
                    <?php 
                        #$teamusers->setPath('teamuser?project_id='.$projectTask->project_id.'&task_id='.$projectTask->id);
                    ?>
                    <?php $paginator = $teamusers; ?>
                    @include('includes.paginator')
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md">
                <a href="{{ route($formRouteCreate) }}" class="btn btn-orange mt-1"><i class="fa fa-plus"></i> Tambah staff</a>
                <a href="{{ route($formTitleIndex) }}" class="btn btn-orange mt-1"><i class="fa fa-eye"></i> Lihat jabatan</a>
            </div>
        </div>
    @else
    <div class="card-body bg-gray-lini-2">
        <div class="alert alert-warning">Belum ada data.</div>
    </div>
    <div class="card-body">
        <div class="col-md">
            <a href="{{ route($formRouteCreate) }}" class="btn btn-orange mt-1"><i class="fa fa-plus"></i> Tambah staff</a>
            <a href="{{ route($formTitleIndex) }}" class="btn btn-orange mt-1"><i class="fa fa-eye"></i> Lihat jabatan</a>
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
