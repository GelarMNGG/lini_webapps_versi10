@extends('layouts.dashboard-datatables')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Daftar clients';
    $formRouteCreate = 'client.create';
    $formRouteEdit = 'client.edit';
    $formRouteDestroy = 'client.destroy';

    //add client contact
    $formClientContactCreate = 'client-contact-person.create';
    $formClientContactShow = 'client-contact-person.show';
    $formClientContactEdit = 'client-contact-person.edit';
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
    <div class="card-header text-center text-uppercase bb-orange"><strong>{{ ucfirst($pageTitle) }}</strong></div>

    @if (isset($clients))
        <div class="card-body bg-gray-lini-2">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table id="" class="display table table-bordered table-bordered dt-responsive nowrap">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Perusahaan</th>
                                        <th>Kontak</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 1; ?>
                                    @foreach ($clients as $data)
                                        <tr>
                                            <td>{{ $i }}</td>
                                            <td>
                                                <div class="row">
                                                    <div class="col-md">
                                                        <strong>{{ strtoupper($data->name) }}</strong>
                                                        <form action="{{ route($formRouteDestroy, $data->id) }}" method="POST">
                                                            @method('DELETE')
                                                            @csrf
                                                            <a href="{{ route($formRouteEdit, $data->id) }}" class='btn btn-icon waves-effect waves-light btn-info t-white'> <i class='fas fa-edit' title='Edit'></i></a>
                                                            <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin akan menghapus data ini?')"><i class="fas fa-times" title='Delete'></i></button>  
                                                        </form>
                                                    </div>
                                                    <div class="col-md">
                                                        <img src="{{ asset('img/clients/'.$data->logo) }}" alt="logo" class="rounded-circle avatar-xl">
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <?php 
                                                    if (Auth::user()->company_id == 1 && (Auth::user()->role == 1 || Auth::user()->department_id == 5)) {
                                                        $contactStatus = $data->cp_count_admin;
                                                    }else{
                                                        $contactStatus = $data->cp_count;
                                                    }
                                                ?>
                                                @if(isset($data->cp_count) && $contactStatus > 0)
                                                    @foreach($clientsContactPersons as $clientsContact)
                                                        @if($clientsContact->company_id == $data->id)
                                                            <strong>{{ ucwords($clientsContact->firstname).' '.ucwords($clientsContact->lastname) }}</strong>

                                                            <div class="float-right text-right">
                                                                @if($publisher_count == 0)
                                                                    @if($clientsContact->publisher_id == $userId && $clientsContact->publisher_id == $userType && $clientsContact->publisher_id == $userDepartment && $clientsContact->publisher_id == $userCompany)
                                                                        <a href="{{ route($formClientContactEdit, $clientsContact->id) }}" class='badge badge-info'> <i class='fas fa-edit' title='Edit'></i> Edit</a>
                                                                        <a href="{{ route($formClientContactShow, $clientsContact->id) }}" class='badge badge-warning'> <i class='fas fa-eye' title='Lihat'></i> Lihat</a>
                                                                    @else
                                                                        <a href="{{ route($formClientContactShow, $clientsContact->id) }}" class='badge badge-warning'> <i class='fas fa-eye' title='Lihat'></i> Lihat</a>
                                                                    @endif
                                                                @else
                                                                    <a href="{{ route($formClientContactShow, $clientsContact->id) }}" class='badge badge-warning'> <i class='fas fa-eye' title='Lihat'></i> Lihat</a>
                                                                    <a href="{{ route($formClientContactEdit, $clientsContact->id) }}" class='badge badge-info'> <i class='fas fa-edit' title='Edit'></i> Edit</a>
                                                                @endif

                                                                @if ($clientsContact->mobile !== null)
                                                                    <br><a href='https://wa.me/{{ $clientsContact->mobile }}?text=Haloo ' target='_blank' class='badge badge-success'> <i class='fab fa-whatsapp' title='Whatsapp'></i> {{ $clientsContact->mobile }}</a>
                                                                @endif

                                                                @if ($clientsContact->status == 0)
                                                                    <br><span class='badge badge-danger'> Private</span>
                                                                @else
                                                                    <br><span class='badge badge-success'> Shared</span>
                                                                @endif
                                                            </div>

                                                            <br>
                                                            <small>
                                                                {{ ucwords($clientsContact->title) }}
                                                            </small>
                                                            <hr>
                                                        @endif
                                                    @endforeach
                                                    <a href="{{ route($formClientContactCreate, 'cid='.$data->id) }}" class="btn btn-orange"><i class="fa fa-plus"></i></a>
                                                @else
                                                    <span class="small">Belum ada data</span>
                                                    <br><a href="{{ route($formClientContactCreate, 'cid='.$data->id) }}" class="btn btn-orange"><i class="fa fa-plus"></i></a>
                                                @endif
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
        </div>
        <div class="card-body">
            <div class="col-md">
                <a href="{{ route($formRouteCreate) }}" class="btn btn-orange"><i class="fa fa-plus"></i> Tambah client</a>
            </div>
        </div>
    @else
        <div class="card-body bg-gray-lini2">
            <div class="alert alert-warning">Belum ada data.</div>
        </div>
        <div class="card-body">
            <div class="col-md">
                <a href="{{ route($formRouteCreate) }}" class="btn btn-orange"><i class="fa fa-plus"></i> Tambah client</a>
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
