@extends('layouts.admin-vue')

@section('content')
<dotxettotnghiepchitiet-box :dt_id="'{{ $dt_id }}'" :qd="'{{ $quyetDinh }}'" :dxtn_id="'{{ $dotxettotnghiep->dxtn_id }}'" :dxtn_ten="'{{ $dotxettotnghiep->dxtn_ten }}'" :parent_url="'{{ $parentUrl }}'"></dotxettotnghiepchitiet-box>
@endsection

@push('scripts')
<script type="text/javascript">
</script>
@endpush
