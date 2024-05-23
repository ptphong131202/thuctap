@extends('layouts.admin-vue')

@section('content')
<dotthi-sinhvien-box :qd="'{{ $quyetDinh }}'" :dt_id="'{{ $dotthi->dt_id }}'" :dt_ten="'{{ $dotthi->dt_ten }}'"></dotthi-sinhvien-box>
@endsection

@push('scripts')
<script type="text/javascript">
</script>
@endpush
