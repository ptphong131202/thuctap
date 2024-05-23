@extends('layouts.admin-vue')

@section('content')
<dotxettotnghiepsinhvien-box :qd="'{{ $quyetDinh }}'" :dxtn_id="'{{ $dotxettotnghiep->dxtn_id }}'"  :dxtn_ten="'{{ $dotxettotnghiep->dxtn_ten }}'" :dt_id="'{{ $dotthi->dt_id }}'" :dt_ten="'{{ $dotthi->dt_ten }}'"></dotthi-sinhvien-box>
@endsection

@push('scripts')
<script type="text/javascript">
</script>
@endpush
