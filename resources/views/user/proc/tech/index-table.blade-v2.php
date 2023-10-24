@extends('layouts.dashboard-datatables')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Daftar teknisi';

    $formRouteIndex = 'user-tech.index';
    $formRouteCreate = 'user-tech.create';
    $formRouteEdit = 'user-tech.edit';
    $formRouteShow = 'user-tech.show';
    $formRouteDestroy = 'user-tech.destroy';
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
        <div class='badge badge-info float-left'>{{ count($techsDatas) }}</div>
        <a href="{{ route ($formRouteIndex,'skin='.$skin)}}" class='badge badge-danger float-right'>Change skin</a>
        <strong>{{ ucfirst($pageTitle) }}</strong>
    </div>

    @if (isset($techsDatas))
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
                                    <th>Ringkasan profil</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; ?>
                                @foreach ($techsDatas as $data)
                                    <tr>
                                        <td>{{ $i }}</td>
                                        <td>
                                            @if(isset($data->firstname))
                                                {{ ucwords($data->firstname).' '.ucwords($data->lastname) }}
                                            @else
                                                {{ ucwords($data->name) }}
                                            @endif
                                        </td>
                                        <td>
                                            <small>
                                                @if(isset($ratingCountAlls) && count($ratingCountAlls) > 0)
                                                    @foreach ($ratingCountAlls as $ratingCountAll)
                                                        @if ($ratingCountAll->tech_id == $data->id)
                                                            <?php 
                                                                if ($ratingCountAll->count > 0) {
                                                                    $ratingValue = $ratingCountAll->totalCount / $ratingCountAll->count; 
                                                                }else{
                                                                    $ratingValue = 0;
                                                                }
                                                            ?>
                                                            @if ($ratingValue >= 5)
                                                                @for ($ri = 0; $ri < 5; $ri++)
                                                                <span class="fa fa-star text-warning"></span>
                                                                @endfor
                                                            @elseif($ratingValue >= 4 && $ratingValue <= 5)
                                                                @for ($ri = 0; $ri < 4; $ri++)
                                                                <span class="fa fa-star text-warning"></span>
                                                                @endfor
                                                                @for ($ri = 0; $ri < 1; $ri++)
                                                                <span class="fa fa-star rating rating-checked"></span>
                                                                @endfor
                                                            @elseif($ratingValue >= 3 && $ratingValue <= 4)
                                                                @for ($ri = 0; $ri < 3; $ri++)
                                                                <span class="fa fa-star text-warning"></span>
                                                                @endfor
                                                                @for ($ri = 0; $ri < 2; $ri++)
                                                                <span class="fa fa-star rating rating-checked"></span>
                                                                @endfor
                                                            @elseif($ratingValue >= 2 && $ratingValue <= 3)
                                                                @for ($ri = 0; $ri < 2; $ri++)
                                                                <span class="fa fa-star text-warning"></span>
                                                                @endfor
                                                                @for ($ri = 0; $ri < 3; $ri++)
                                                                <span class="fa fa-star rating rating-checked"></span>
                                                                @endfor
                                                            @elseif($ratingValue >= 1 && $ratingValue <= 2)
                                                                @for ($ri = 0; $ri < 1; $ri++)
                                                                <span class="fa fa-star text-warning"></span>
                                                                @endfor
                                                                @for ($ri = 0; $ri < 4; $ri++)
                                                                <span class="fa fa-star rating rating-checked"></span>
                                                                @endfor
                                                            @else
                                                                @for ($ri = 0; $ri < 5; $ri++)
                                                                <span class="fa fa-star rating text-checked"></span>
                                                                @endfor
                                                            @endif

                                                            ({{ $ratingCountAll->count }} voter)
                                                        @else
                                                            @for ($ri = 0; $ri < 5; $ri++)
                                                                <span class="fa fa-star rating text-checked"></span>
                                                            @endfor
                                                        @endif
                                                    @endforeach
                                                @else
                                                    @for ($ri = 0; $ri < 5; $ri++)
                                                        <span class="fa fa-star rating text-checked"></span>
                                                    @endfor
                                                @endif
                                                
                                                <br> Keahlian: 
                                                @if(isset($data->skill_id))
                                                    @foreach($techSkillsDatas as $skillData)
                                                        @if($skillData->id == $data->skill_id)
                                                            <span>{{ $skillData->name }}</span>
                                                        @endif
                                                    @endforeach
                                                @else
                                                    <span class="text-danger">-</span>
                                                @endif
                                                <br> Proyek terbaru: <span class="text-danger">-</span>
                                                <br> Tes psikologi tgl: {{ $data->test_psychology_date ? date('l, d F Y',strtotime($data->test_psychology_date)) : '-'}} 
                                                    @if(isset($data->test_psychology_date))
                                                        @if($data->test_psychology_status == 2)
                                                            <span class="text-success">| Lulus seleksi</span>
                                                        @elseif($data->test_psychology_status == 1)
                                                            <span class="text-danger">| Gagal seleksi</span>
                                                        @else
                                                            <span class="text-info">| <a href="#">Check</a></span>
                                                        @endif
                                                    @endif
                                                <br> Tes assessment tgl: {{ $data->test_assessment_date ? date('l, d F Y',strtotime($data->test_assessment_date)) : '-'}} 
                                                    @if(isset($data->test_assessment_date))
                                                        @if($data->test_assessment_result == 100)
                                                            <span class="text-success">| Lulus ({{ $data->test_assessment_result }})</span>
                                                        @else
                                                            <span class="text-danger">| Gagal ({{ $data->test_assessment_result }}/100)</span>
                                                        @endif
                                                    @endif
                                            </small>
                                        </td>
                                        <td>
                                            @if($data->active == 1)
                                                <div class="badge badge-success float-right">Active</div>
                                            @else
                                                <div class="badge badge-danger float-right">Inactive</div>
                                            @endif
                                            <br>
                                            @if($data->test_psychology_result == 'intj')
                                                <div class="badge badge-success float-right">Cocok</div>
                                            @else
                                                <div class="badge badge-danger float-right">Tidak cocok</div>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($data->mobile !== null)
                                                <a href='https://wa.me/{{ $data->mobile }}?text=Haloo ' target='_blank' class='btn btn-icon waves-effect waves-light btn-success t-white mt-1'> <i class='fab fa-whatsapp' title='Whatsapp'></i></a>
                                            @endif
                                            <form action="{{ route($formRouteDestroy, $data->id) }}" style="display:inline-block;" method="POST">
                                                @method('DELETE')
                                                @csrf
                                                <a href="{{ route($formRouteEdit, $data->id) }}" class='btn btn-icon waves-effect waves-light btn-info t-white mt-1'> <i class='fas fa-edit' title='Edit'></i></a>
                                                <button type="submit" class="btn btn-danger mt-1" onclick="return confirm('Apakah Anda yakin akan menghapus data ini?')"><i class="fas fa-times" title='Delete'></i></button>  
                                            </form>
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
                <a href="{{ route($formRouteCreate) }}" class="btn btn-orange mt-1"><i class="fa fa-plus"></i> Tambah teknisi</a>
            </div>
        </div>
    @else
    <div class="card-body bg-gray-lini-2">
        <div class="alert alert-warning">Belum ada data.</div>
    </div>
    <div class="card-body">
        <a href="{{ route($formRouteCreate) }}" class="btn btn-orange mt-1"><i class="fa fa-plus"></i> Tambah teknisi</a>
    </div>
    @endif
</div> <!-- container-fluid -->
@endsection

@section ('script')
<script>
    $(document).ready(function() {
        $('table.display').DataTable({
            order: [[ 0, 'desc' ], [ 5, 'asc' ]]
        });
    } );
</script>
@endsection
