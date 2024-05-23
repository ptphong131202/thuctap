@extends('layouts.admin-vue')

@section('content')
<dotthimonhoclop-box :qd="'{{ $quyetDinh }}'" :dt_id="'{{ $dotthi->dt_id }}'" :dt_ten="'{{ $dotthi->dt_ten }}'"
    :lh_id="'{{ $lophoc->lh_id }}'" :lh_ten="'{{ $lophoc->lh_ten }}'" :url_import="'{{$url}}'"></dotthimonhoclop-box>
@endsection

@push('scripts')
<script type="text/javascript">
</script>
@endpush
